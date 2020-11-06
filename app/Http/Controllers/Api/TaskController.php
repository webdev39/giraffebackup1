<?php

namespace App\Http\Controllers\Api;

use App\Events\ChangeActivityLogEvent;
use App\Events\Eloquent\ChangedTaskEvent;
use App\Events\Eloquent\Saved\CreatedTaskEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttachMemberToTaskRequest;
use App\Http\Requests\ChangeSubTaskOrder;
use App\Http\Requests\DetachMemberToTaskRequest;
use App\Http\Requests\NotificationSubscriptionRequest;
use App\Http\Requests\SubscribeAndMemberToTaskRequest;
use App\Http\Requests\UpdateSubTaskRequest;
use App\Http\Requests\ChangeSubTaskStatusRequest;
use App\Http\Requests\ChangeTaskOrderRequest;
use App\Http\Requests\ChangeTaskWorkflowRequest;
use App\Http\Requests\CreateSubTaskRequest;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\GetTaskListIds;
use App\Http\Resources\SubTaskResource;
use App\Http\Resources\TaskResource;
use App\Listeners\ActivityLogListener;
use App\Models\BudgetType;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTenant;
use App\Repositories\BoardRepositoryEloquent;
use App\Services\ActivityLog\ActivityLogService;
use App\Services\Board\BoardService;
use App\Services\Budget\BudgetService;
use App\Services\Notification\NotificationService;
use App\Services\Task\TaskService;
use App\Services\TaskSortOrder\TaskSortOrderService;
use App\Services\Tenant\TenantService;
use App\Services\Timer\TimerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ChangeTaskSortWeightRequest;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use function foo\func;

class TaskController extends Controller
{
    /**
     * @var BoardService
     */
    private $boardService;
    /**
     * @var BudgetService
     */
    private $budgetService;
    /**
     * @var NotificationService
     */
    private $notifyService;
    /**
     * @var TaskService
     */
    private $taskService;
    /**
     * @var TenantService
     */
    private $tenantService;
    /**
     * @var BoardRepositoryEloquent
     */
    private $boardRepository;
    /**
     * @var TimerService
     */
    private $timerService;
    /**
     * @var TaskSortOrderService
     */
    private $taskSortOrderService;

    /**
     * TaskController constructor.
     * @param BoardService $boardService
     * @param BudgetService $budgetService
     * @param NotificationService $notificationService
     * @param TaskService $taskService
     * @param BoardRepositoryEloquent $boardRepository
     * @param TenantService $tenantService
     * @param TimerService $timerService
     * @param TaskSortOrderService $taskSortOrderService
     */
    public function __construct(
        BoardService $boardService,
        BudgetService $budgetService,
        NotificationService $notificationService,
        TaskService $taskService,
        BoardRepositoryEloquent $boardRepository,
        TenantService $tenantService,
        TimerService $timerService,
        TaskSortOrderService $taskSortOrderService
    )
    {
        $this->boardService = $boardService;
        $this->budgetService = $budgetService;
        $this->notifyService = $notificationService;
        $this->taskService = $taskService;
        $this->tenantService = $tenantService;
        $this->boardRepository = $boardRepository;
        $this->timerService = $timerService;
        $this->taskSortOrderService = $taskSortOrderService;
    }

    /**
     * @return JsonResponse
     */
    public function getLatestActiveTasks()
    {
        $tasks = $this->taskService->getLatestActiveBoardsByUserTenantId(Auth::userTenantId());

        return response()->json([
            'tasks' => TaskResource::collection($tasks)
        ]);
    }

    /**
     * Method for get list tasks
     * @param GetTaskListIds $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(GetTaskListIds $request)
    {
        $tasks = $this->taskService->getTaskWithRelationsByIds($request->ids);

        return response()->json([
            'tasks' => TaskResource::collection($tasks)
        ]);
    }

    /**
     * Method for get list tasks by board id
     * @param int $boardId
     * @param Request $request
     * @return App\Collections\TaskCollection
     */
    public function getTaskByBoardId(int $boardId, Request $request)
    {
        $tasks = $this->taskService->getActiveTasksByBoardId($boardId, Auth::userTenantId(), (bool)$request->query('hide_done'));

        return response()->json([
            'tasks' => TaskResource::collection($tasks)
        ]);
    }

    /**
     * @param int $taskId
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $taskId)
    {
        $task = $this->taskService->getTaskById((int)$taskId);
        $this->authorize('getAccess', $task);

//        $this->taskService->markRelatedUnreadNotificationsAsRead($task, Auth::user());
        $task = $this->taskService->getTaskWithRelationsById($taskId, Auth::userTenant()->id);

        return response()->json([
            'task' => new TaskResource($task)
        ]);
    }

    /**
     * @param int $taskId
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function setRead(int $taskId)
    {
        $task = $this->taskService->getTaskById((int)$taskId);
        $this->authorize('getAccess', $task);
        $this->taskService->markRelatedUnreadNotificationsAsRead($task, Auth::user());
        return response()->json([
            'task' => new TaskResource($task)
        ]);
    }

    /**
     * @param CreateTaskRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function create(CreateTaskRequest $request)
    {
        $board = $this->boardRepository->findOrFail((int)$request->get('board_id'));
        $this->authorize('create', [Task::class, $board]);
        $userTenant = Auth::userTenant();

        try {
            $defaultBudgetTypeId = app('FieldRepo')->getDefaultBudgetType()->id;
            $attributes = [
                'board_id' => $board->id,
                'parent_id' => $request->get('parent_id', 0),
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'deadline' => $request->get('deadline'),
                'planned_deadline' => $request->get('planned_deadline'),
                'soft_budget' => $request->get('soft_budget'),
                'priority_id' => $request->get('priority_id'),
                'sort_weight' => $request->get('sort_weight', time()),
                'budget_type_id' => $request->get('budget_type_id', $defaultBudgetTypeId),
            ];

            if ($request->get('is_draft')) {
                $draftTask = $this->taskService->firstOrCreateDraftTask($attributes, $userTenant->id);
            } else {
                if ($request->get('draft_task_id') && false) {
                    $task = $this->taskService->draftTaskToTask($attributes, $request->get('draft_task_id'));
                    $draftTask = $this->taskService->firstOrCreateDraftTask([
                        'creator_id' => Auth::id(),
                        'board_id' => $board->id,
                        'priority_id' => $request->get('priority_id')
                    ], $userTenant->id);
                } else {
                    $attributes['creator_id'] = Auth::id();
                    $task = $this->taskService->create($attributes, $userTenant->id);

                    if ($request->filled('subscribers')) {
                        $getSubscribers = UserTenant::whereIn('id', $request->subscribers['notify'])
                            ->get()
                            ->pluck('user_id')
                            ->toArray();

                        $subscribers = [];
                        foreach ($getSubscribers as $s) {
                            $subscribers[]['user_id'] = $s;
                        }

                        $task->notifySubscriptions()->createMany($subscribers);
                        $task->taskSubscribers()->attach($request->subscribers['task']);


                        if ($request->filled('draft_task_id')) {
                            $draftTask = $this->taskService->getDraftTaskModelById($request->get('draft_task_id'));
                            $draftTask->notifySubscriptions()->delete();
                            $this->notifyService->taskSubscribe($draftTask, Auth::id());
                            $draftTask->taskSubscribers()->sync($userTenant->id);
                        }

                    } else {
                        $this->notifyService->taskSubscribe($task, Auth::id());
                        $this->taskService->attachUserTenantToTask($task, $userTenant->id);
                    }

                    event(new CreatedTaskEvent(Task::findOrFail($task->id)));
                }
            }

        } catch (\Exception $e) {
            dump($e);
            Log::error('error:' . $e->getCode() . ' ' . $e->getMessage(), $e->getTrace());
            abort(500, 'Error creating task');
        }

        $response = [];

        if (isset($task)) {
            $response['task'] = new TaskResource($this->taskService->getTaskWithRelationsById(
                $task->id,
                $userTenant->id)
            );
        }

        if (isset($draftTask)) {
            $response['draft_task'] = new TaskResource($this->taskService->getTaskWithRelationsById(
                $draftTask->id,
                $userTenant->id)
            );
        }

        return response()->json($response);
    }

    /**
     * @param UpdateTaskRequest $request
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function update(UpdateTaskRequest $request)
    {
        $task = $this->taskService->getTaskById((int)$request->get('task_id'));
        $this->authorize('update', $task);

        $userTenant = Auth::userTenant();
        $attributes = array_intersect_key($request->all(), array_flip([
            'board_id',
            'name',
            'description',
            'priority_id',
            'deadline',
            'planned_deadline',
            'soft_budget',
            'hard_budget',
            'budget_type_id',
        ]));

        $this->taskService->update($attributes, $task->id, $userTenant->id);

        app('RepeatSer')->createOrUpdateRepeat([
            'time_unit' => $request->get('repeat_unit'),
            'time_interval' => $request->get('repeat_interval'),
            'started_at' => $request->get('repeat_started_at'),
            'ended_at' => $request->get('repeat_ended_at'),
            'user_tenant_id' => $userTenant->id,
        ], $task->id);

        $task = $this->taskService->getTaskWithRelationsById($task->id, $userTenant->id);

        if (Task::find($task->id)) {
            event(new ChangedTaskEvent(Task::find($task->id)));
        }

        return response()->json([
            'task' => new TaskResource($task)
        ]);
    }

    /**
     * @param int $taskId
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function remove(int $taskId)
    {
        $task = $this->taskService->getTaskById($taskId);

        $this->authorize('delete', $task);

        if (!$this->taskService->checkCanDeleteTask($task->id)) {
            abort(403, 'The task has logged time! You can only change workflow');
        }

        $this->taskService->deleteTask($task->id);

        $board = $task->board->first();
        $action = 'deleted';

        $message = __('activity.task.action.' . $action, [
            'action' => $action,
            'sender' => Auth::user()->full_name,
            'task' => $task->name,
        ]);

        ActivityLogService::saveActivityLog(
            $task,
            Auth::user(),
            $message,
            [
                'action' => $action,
                'task_id' => $task->id,
                'board_id' => $board->id,
                'group_id' => $board->group->id,
            ]
        );

        $this->taskService->markRelatedUnreadNotificationsAsRead($task, Auth::user());

        return response()->json(['message' => 'Success']);
    }

    public function subscribeAndAssign(SubscribeAndMemberToTaskRequest $request)
    {
        $task = Task::withoutGlobalScope('draft')->findOrFail($request->get('task_id'));
        $this->authorize('assignMember', $task);
        $usersTenantsIds = array_wrap($request->get('user_tenant_id'));
        foreach ($usersTenantsIds as $userTenantId) {
            $userTenant = UserTenant::findOrFail($userTenantId);
            $this->taskService->subscribeAndAssign($task, $userTenant);
        }

        return response()->json(['message' => 'Success']);
    }

    /**
     * @param AttachMemberToTaskRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function assign(AttachMemberToTaskRequest $request)
    {
        $task = $this->taskService->getTaskById($request->get('task_id'));

        $this->authorize('assignMember', $task);

        $userTenantIds = $request->get('user_tenant_id');
        if (!is_array($userTenantIds)) {
            $userTenantIds = [$userTenantIds];
        }

        foreach ($userTenantIds as $userTenantId) {
            $tenantAssignee = $this->tenantService->getUserTenantById($userTenantId);
            $this->taskService->attachUserTenantToTask($task, $tenantAssignee->id);
        }

        return response()->json(['message' => 'Success']);
    }

    /**
     * @param DetachMemberToTaskRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function unassign(DetachMemberToTaskRequest $request)
    {
        $task = $this->taskService->getTaskById($request->get('task_id'));
        $this->authorize('unAssignMember', $task);

        $userTenantIds = $request->get('user_tenant_id');
        if (!is_array($userTenantIds)) {
            $userTenantIds = [$userTenantIds];
        }
        foreach ($userTenantIds as $userTenantId) {
            $tenantAssignee = $this->tenantService->getUserTenantById($userTenantId);
            $this->taskService->detachUserTenantToTask($task, $tenantAssignee->id);
        }

        return response()->json(['message' => 'Success']);
    }

    /**
     * @param NotificationSubscriptionRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function subscribe(NotificationSubscriptionRequest $request)
    {
        $task = $this->taskService->getTaskById($request->get('task_id'));

        $this->authorize('assignMember', $task);

        $userIds = is_array($request->get('user_id')) ? $request->get('user_id') : [$request->get('user_id')];
        foreach ($userIds as $userId) {
            $this->notifyService->taskSubscribe($task, $userId);
        }

        return response()->json(['message' => 'Success']);
    }

    /**
     * @param NotificationSubscriptionRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function unsubscribe(NotificationSubscriptionRequest $request)
    {
        $task = $this->taskService->getTaskById($request->get('task_id'));

        $this->authorize('unAssignMember', $task);

        $userIds = is_array($request->get('user_id')) ? $request->get('user_id') : [$request->get('user_id')];
        foreach ($userIds as $userId) {
            $this->notifyService->taskUnsubscribe($task, $userId);
        }

        return response()->json(['message' => 'Success']);
    }

    /**
     * @param ChangeTaskOrderRequest $request
     * @param string $type
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function changeOrder(ChangeTaskOrderRequest $request, string $type)
    {
        return response()->json(['message' => 'Success']);

        $sortType = TaskSortOrderService::getSortOrderTypeByKey($type);
        $modelID = app('TaskSortOrderSer')->getSortOrderModelId($sortType, $request->all(['filter_id']));

        app('TaskSortOrderSer')->updateTaskOrders($sortType, $modelID, $request->get('order'));

        return response()->json(['message' => 'Success']);
    }

    /**
     * @param ChangeTaskSortWeightRequest $request
     * @param $taskId
     * @return JsonResponse
     * @throws \Throwable
     */
    public function changeSortWeight(ChangeTaskSortWeightRequest $request, $taskId)
    {
        /** @var Task $task */
        $task = $this->taskService->getTaskById($taskId);

        abort_if(!$task, 404, 'Task not found');

        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->taskService->update($request->all([
            'sort_weight'
        ]), $task->id, $userTenant->id);

        return response()->json(['message' => 'Success']);
    }

    /**
     * Sub Task
     */

    /**
     * @param int $taskId
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function subTaskList(int $taskId)
    {
        $task = $this->taskService->getTaskById($taskId);

        $this->authorize('getAccess', $task);

        $subTasks = $this->taskService->getSubTasksByTaskId($taskId);

        return response()->json([
            'sub_tasks' => SubTaskResource::collection($subTasks)
        ]);
    }

    /**
     * @param CreateSubTaskRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createSubTask(CreateSubTaskRequest $request)
    {
        $task = $this->taskService->getTaskById($request->get('task_id'));

        $this->authorize('getAccess', $task);

        $attributes = $request->all(['name']);
        $attributes['creator_id'] = Auth::id();
        $subTask = $this->taskService->createSubTask($attributes, $request->get('task_id'));
        $subTask = $this->taskService->getSubTaskById($subTask->id);

        return response()->json([
            'sub_tasks' => new SubTaskResource($subTask)
        ]);
    }

    /**
     * @param UpdateSubTaskRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateSubTask(UpdateSubTaskRequest $request)
    {
        $task = $this->taskService->getTaskById($request->get('task_id'));

        $this->authorize('update', $task);

        $subTask = $this->taskService->updateSubTask($request->all(['name']), $request->get('sub_task_id'));
        $subTask = $this->taskService->getSubTaskById($subTask->id);

        return response()->json([
            'sub_tasks' => new SubTaskResource($subTask)
        ]);
    }

    /**
     * @param int $subTaskId
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function removeSubTask(int $subTaskId)
    {
        $subTask = $this->taskService->getSubTaskById($subTaskId);
        $task = $this->taskService->getTaskById($subTask->task_id);

        $this->authorize('delete', $task);

        $this->taskService->removeSubTask($subTaskId);

        return response()->json(['message' => 'Success']);
    }

    /**
     * @param ChangeSubTaskStatusRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function changeSubTaskStatus(ChangeSubTaskStatusRequest $request)
    {
        $task = $this->taskService->getTaskById($request->get('task_id'));
        $subTask = $this->taskService->getSubTaskById($request->get('sub_task_id'));

        $this->authorize('update', $task);

        $attributes = $request->all(['is_completed']);
        if (!empty($attributes['is_completed']) && $attributes['is_completed']) {
            $attributes['completed_by_id'] = Auth::id();
        }

        $subTask = $this->taskService->updateSubTask($attributes, $subTask->id);

        return response()->json([
            'sub_tasks' => new SubTaskResource($subTask)
        ]);
    }

    /**
     * @param ChangeSubTaskOrder $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function changeSubTaskOrder(ChangeSubTaskOrder $request)
    {
        $task = $this->taskService->getTaskById($request->get('task_id'));

        $this->authorize('update', $task);

        $this->taskService->changeSubTaskOrder($task->id, $request->get('order'));

        $subTasks = $this->taskService->getSubTasksByTaskId($task->id);

        return response()->json([
            'sub_tasks' => SubTaskResource::collection($subTasks)
        ]);
    }

    /**
     * @param ChangeTaskWorkflowRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function changeWorkflow(ChangeTaskWorkflowRequest $request)
    {
        $task = $this->taskService->getTaskById($request->get('task_id'));

        $this->authorize('changeWorkflow', $task);

        $timers = $this->timerService->getTimersByTaskId($task->id);

        if (!$timers) {
            abort(500, 'Unable to delete a task because it has tracked time');
        }

        $this->taskService->changeTaskWorkflow($task->id, $request->get('is_done'));

        $this->taskSortOrderService->resetTaskOrderByTaskId($task->id);

        $task = $this->taskService->getTaskWithRelationsById($task->id, Auth::userTenantId());

        return response()->json([
            'task' => new TaskResource($task)
        ]);
    }
}
