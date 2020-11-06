<?php

namespace App\Services\Task;

use App\Collections\TaskCollection;
use App\Collections\TimerCollection;
use App\Events\ChangeActivityLogEvent;
use App\Events\Eloquent\ChangedTaskEvent;
use App\Events\TaskSubscriptionAndAssignmentEvent;
use App\Models\NotificationSubscription;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTenant;
use App\Models\UserTenantTask;
use App\Repositories\SubTaskRepositoryEloquent;
use App\Repositories\TaskRepositoryEloquent;
use App\Repositories\UserTenantTaskRepositoryEloquent;
use App\Services\ActivityLog\ActivityLogService;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class TaskService
 *
 * @package App\Services\Task
 */
class TaskService extends BaseService
{
    /** @var UserTenantTaskRepositoryEloquent */
    private $userTenantTaskRepo;

    /** @var TaskRepositoryEloquent */
    private $taskRepo;

    /** @var SubTaskRepositoryEloquent */
    private $subTaskRepo;

    /**
     * TaskService constructor.
     */
    public function __construct()
    {
        $this->taskRepo             = app('TaskRepo');
        $this->subTaskRepo          = app('SubTaskRepo');
        $this->userTenantTaskRepo   = app('UserTenantTaskRepo');
    }

    /**
     * @param int $userTenantId
     * @param int $lastCount
     *
     * @return Collection
     */
    public function getLatestActiveBoardsByUserTenantId(int $userTenantId, int $lastCount = 5)
    {
        return $this->taskRepo->getLatestActiveBoardsByUserTenantId($userTenantId, $lastCount);
    }

    /**
     * @param int $taskId
     *
     * @return Task
     */
    public function getTaskById(int $taskId)
    {
        return $this->taskRepo->scopeQuery(function ($query) {
            return $query->withoutGlobalScopes();
        })->findOrFail($taskId);
    }

    /**
     * @param int $boardId
     *
     * @param int|null $userTenantId
     * @param bool $hide_done
     * @return mixed
     */
    public function getActiveTasksByBoardId(int $boardId, int $userTenantId = null, bool $hide_done)
    {
        $tasks = app('TaskRepo')->getActiveTasksByBoardId([$boardId], $userTenantId, $hide_done);
        return $this->addTaskRelations($tasks);
    }

    /**
     * @param int $budgetId
     *
     * @return mixed
     */
    public function getTaskByBudgetId(int $budgetId)
    {
        return $this->taskRepo->findWhere(['budget_id' => $budgetId])->first();
    }

    public function subscribeAndAssign(Task $task, UserTenant $userTenant)
    {
        if(!UserTenantTask::whereTaskId($task->id)->whereUserTenantId($userTenant->id)->exists()) {
            DB::table('user_tenant_task')
                ->insert([
                    'user_tenant_id' => $userTenant->id,
                    'task_id' => $task->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }
        if(!NotificationSubscription::whereTaskId($task->id)->whereUserId($userTenant->user->id)->exists()) {
            DB::table('notification_subscriptions')
                ->insert([
                    'user_id' => $userTenant->user->id,
                    'task_id' => $task->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        event(new TaskSubscriptionAndAssignmentEvent(\Auth::user(), $task, $userTenant->user));
        event(new ChangedTaskEvent($task));
    }

    /**
     * @param Task $task
     * @param int  $attachUserTenantId
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function attachUserTenantToTask(Task $task, int $attachUserTenantId)
    {
        $this->userTenantTaskRepo->attachUserTenantToTask($task->id, $attachUserTenantId);
        return event(new ChangedTaskEvent($task));
    }

    /**
     * @param Task $task
     * @param int  $attachUserTenantId
     *
     * @return bool|null
     * @throws \Exception
     */
    public function detachUserTenantToTask(Task $task, int $attachUserTenantId)
    {
        $this->userTenantTaskRepo->detachUserTenantToTask($task->id, $attachUserTenantId);
        return event(new ChangedTaskEvent($task));
    }

    /**
     * @param int $taskId
     *
     * @return int
     */
    public function deleteTask(int $taskId) : int
    {
        return $this->taskRepo->delete($taskId);
    }

    /**
     * @param $taskId
     *
     * @return bool
     * @throws \Exception
     */
    public function checkCanDeleteTask($taskId) : bool
    {
        $minutes = app('TimerSer')->getTaskSpentTime([$taskId]);

        return (bool) !$minutes;
    }

    /**
     * @param Collection $tasks
     *
     * @return TaskCollection|Collection
     */
    public function addTaskRelations(Collection $tasks)
    {
        $taskIds            = $tasks->pluck('id')->unique()->toArray();

        $subTasks           = app('SubTaskRepo')->getSubTasksByTaskIds($taskIds);
        $taskSubscribers    = app('UserTenantRepo')->getTaskSubscribersByTaskIds($taskIds);
        $notifySubscribers  = app('UserTenantRepo')->getNotifySubscribersByTaskIds($taskIds);

        $countComments      = app('CommentRepo')->getCountCommentsByTaskIds($taskIds);
        $countAttachments   = app('CommentRepo')->getCountAttachmentsByTaskIds($taskIds);
        $countDoneSubTasks  = app('SubTaskRepo')->getCountSubTasksByTaskIds($taskIds, true);
        $countOpenSubTasks  = app('SubTaskRepo')->getCountSubTasksByTaskIds($taskIds, false);

        $timers             = app('TimerRepo')->getTimersByTaskIds($taskIds);
        $timerIds           = $timers->pluck('id')->unique()->toArray();

        $pauses             = app('PauseRepo')->getPausesByTimerIds($timerIds);

        $timers = TimerCollection::make($timers)->setAttributes([
            'pauses' => $pauses->groupBy('timer_id'),
        ]);

        $tasks = TaskCollection::make($tasks);
        $tasks->setAttributes([
            'timers'                => $timers->groupBy('task_id'),
            'sub_tasks'             => $subTasks->groupBy('task_id'),
            'task_subscribers'      => $taskSubscribers->groupBy('task_id'),
            'notify_subscribers'    => $notifySubscribers->groupBy('task_id'),
        ]);

        $tasks->setCountAttributes([
            'comment'               => $countComments,
            'attachment'            => $countAttachments,
            'done_sub_task'         => $countDoneSubTasks,
            'open_sub_task'         => $countOpenSubTasks,
        ]);

        return $tasks;
    }

    /**
     * @param array    $taskIds
     * @param int|null $userTenantId
     *
     * @return TaskCollection|Collection
     */
    public function getTaskWithRelationsByIds(array $taskIds, int $userTenantId = null)
    {
        $tasks = $this->taskRepo->getTasksByIds($taskIds, $userTenantId);

        return $this->addTaskRelations($tasks);
    }

    /**
     * @param int      $taskId
     * @param int|null $userTenantId
     *
     * @return mixed
     */
    public function getTaskWithRelationsById(int $taskId, int $userTenantId = null)
    {
        return $this->getTaskWithRelationsByIds([$taskId], $userTenantId)->first();
    }

    /**
     * @param int $boardId
     * @param int $userTenantId
     *
     * @return mixed|null
     */
    public function getDraftTask(int $boardId, int $userTenantId)
    {
        return $this->taskRepo->getDraftTask($boardId, $userTenantId);
    }

    /**
     * @param int $draftTaskId
     *
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    public function getDraftTaskModelById(int $draftTaskId)
    {
        return $this->taskRepo->getDraftTaskModelById($draftTaskId);
    }

    /**
     * @param array $attributes
     * @param int   $userTenantId
     *
     * @return mixed|null
     * @throws \Throwable
     */
    public function firstOrCreateDraftTask(array $attributes, int $userTenantId)
    {
        if ($task = $this->getDraftTask($attributes['board_id'], $userTenantId)) {
            return $task;
        }

        $attributes['draft'] = true;
        return $this->create($attributes, $userTenantId);
    }

    /**
     * @param array $attributes
     * @param       $userTenantId
     *
     * @return mixed
     * @throws \Throwable
     */
    public function create(array $attributes, $userTenantId)
    {
        return DB::transaction(function () use ($attributes, $userTenantId) {
            if (!isset($attributes['budget_id'])) {
                $budget = app('BudgetSer')->createDefaultBudget(
                    $attributes['soft_budget']??'00:00'
                );
                $attributes['budget_id'] = $budget->id;
            }

            if (!isset($attributes['priority_id']) || !$attributes['priority_id']) {
                $priority = app('PrioritySer')->getPrimaryPriorityByBoardId($attributes['board_id']);

                if($priority) {
                    $attributes['priority_id'] = $priority->id;
                }
            }

            if (isset($attributes['draft']) && $attributes['draft']) {
                $attributes['draft'] = $userTenantId;
            }

            if(empty($attributes['sort_weight'])) {
                $attributes['sort_weight'] = time();
            }


            $task = Task::create($attributes);

            if (isset($attributes['planned_deadline'])) {
                app('PersonalDeadlineRepo')->updateOrCreateDeadline($attributes['planned_deadline'], $task->id, $userTenantId);
                $message = __('activity.task.changed.planned_deadline', [
                    'action'    => 'changed',
                    'task'      => $task->name,
                    'sender'    => Auth::user()->full_name,
                    'new_value' => $attributes['planned_deadline']

                ]);
               // ActivityLogService::customTaskAction($task, Auth::user(), $message,'changed','planned_deadline');
            }

            app('BoardTaskRepo')->create([
                'board_id'  => $attributes['board_id'],
                'task_id'   => $task->id,
            ]);

            return $task;
        });
    }

    /**
     * @param array $attributes
     * @param int    $draftTaskId
     *
     * @return \Illuminate\Database\Eloquent\Model|mixed
     * @throws \Throwable
     */
    public function draftTaskToTask(array $attributes, int $draftTaskId)
    {
        return $this->taskRepo->draftTaskToTask($attributes, $draftTaskId);
    }

    /**
     * @param int $boardId
     * @param int $priorityId
     *
     * @return bool
     */
    public function setPrimaryPriority(int $boardId, int $priorityId)
    {
        $priority   = app('PrioritySer')->getPrimaryPriorityByBoardId($boardId);
        $tasks      = app('TaskRepo')->getTasksByBoardIds([$boardId]);

        $taskIds = $tasks->where('priority_id', $priorityId)->pluck('id')->toArray();

        if ($taskIds) {
            app('TaskRepo')->updatePriority($taskIds, $priority->id);

            return true;
        }

        return false;
    }

    /**
     * @param array $attributes
     * @param int   $taskId
     * @param int   $userTenantId
     *
     * @return mixed
     * @throws \Throwable
     */
    public function update(array $attributes, int $taskId, int $userTenantId)
    {
        return DB::transaction(function () use ($attributes, $taskId, $userTenantId) {
            /** @var Task $task */
            $task = $this->taskRepo->scopeQuery(function ($query) {
                return $query->withoutGlobalScopes();
            })->find($taskId);

            if ($task->view_name == 'kanban') {
                app('TaskSortOrderSer')->resetTaskOrderByTaskId($task->id);
            }

            if (array_key_exists('planned_deadline', $attributes) && $task->getPlannedDeadlineAttribute() !== $attributes['planned_deadline']) {
                if($attributes['planned_deadline']) {
                    app('PersonalDeadlineRepo')->updateOrCreateDeadline($attributes['planned_deadline'], $task->id, $userTenantId);
                } else {
                    app('PersonalDeadlineRepo')->removeDeadline($task->id, $userTenantId);
                }

                $message = __('activity.task.changed.planned_deadline', [
                    'action'    => 'changed',
                    'task'      => $task->name,
                    'sender'    => Auth::user()->full_name,
                    'new_value' => $attributes['planned_deadline']
                ]);

                ActivityLogService::customTaskAction($task, Auth::user(), $message,'changed','planned_deadline');
            }

            if (array_key_exists('board_id', $attributes) && $attributes['board_id'] !== $task->board_id) {
                app('BoardTaskRepo')->attachToOtherBoard($attributes['board_id'], $task->id);

                $attributes['priority_id'] = app('PrioritySer')->getPrimaryPriorityByBoardId($attributes['board_id'])->id;
            }

            $attributes['budget_id'] = app('BudgetSer')->createOrUpdateBudget($attributes, $task->budget_id)->id;

            $task = $this->taskRepo->scopeQuery(function ($query) {
                return $query->withoutGlobalScopes();
            })->update($attributes, $taskId);

            return $task;
        });
    }

    /**
     * Sub Task
     */

    /**
     * @param int $taskId
     *
     * @return Collection
     */
    public function getSubTasksByTaskId(int $taskId)
    {
        return $this->subTaskRepo->getSubTasksByTaskIds([$taskId]);
    }

    /**
     * @param int $subTaskId
     *
     * @return Collection
     */
    public function getSubTaskById(int $subTaskId)
    {
        return $this->subTaskRepo->getSubTaskById($subTaskId);
    }

    /**
     * @param array $attributes
     * @param int   $taskId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createSubTask(array $attributes, int $taskId)
    {
        return $this->subTaskRepo->create(array_merge($attributes, [
            'task_id' => $taskId
        ]));
    }

    /**
     * @param array $attributes
     * @param int   $subTaskId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateSubTask(array $attributes, int $subTaskId)
    {
        return $this->subTaskRepo->update($attributes, $subTaskId);
    }

    /**
     * @param int $subTaskId
     *
     * @return int
     */
    public function removeSubTask(int $subTaskId)
    {
        return $this->subTaskRepo->delete($subTaskId);
    }

    /**
     * @param int   $taskId
     * @param array $orders
     *
     * @return Collection
     * @throws \Throwable
     */
    public function changeSubTaskOrder(int $taskId, array $orders)
    {
        return $this->subTaskRepo->changeSubTaskOrder($taskId, $orders);
    }

    /**
     * @param int $taskId
     *
     * @return mixed
     */
    public function getTaskDetailsById(int $taskId)
    {
        return $this->taskRepo->getTasksByIds([$taskId])->first();
    }

    /**
     * @param int  $task
     * @param bool $isDone
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function changeTaskWorkflow(int $task, $isDone = false)
    {
        return $this->taskRepo->changeTaskWorkflow($task, $isDone);
    }

    /**
     * @param array $taskIds
     * @param bool  $isArchive
     *
     * @throws \Throwable
     */
    public function changeIsArchiveTaskByIds(array $taskIds, bool $isArchive = false)
    {
        return $this->taskRepo->changeIsArchiveTaskByIds($taskIds, $isArchive);
    }

    /**
     * @param array $taskIds
     *
     * @return bool|null
     * @throws \Exception
     */
    public function removeTaskByIds(array $taskIds)
    {
        return $this->taskRepo->removeTaskByIds($taskIds);
    }

    /**
     * @param UserTenant $userTenant
     * @param $query
     * @return \Laravel\Scout\Builder
     */
    public function searchForUserTenant(UserTenant $userTenant, $query)
    {
        return $this->taskRepo->searchForTenantUser($userTenant, $query);
    }

    /**
     * Mark unread notifications related to the task and user as READ
     * @param Task $task
     * @param User $user
     */
    public function markRelatedUnreadNotificationsAsRead(Task $task, User $user)
    {
        $this->getTaskUnreadNotificationsByUser($task->id, $user)->each(function(DatabaseNotification $notification) {
            $notification->markAsRead();
        });
    }

    public function getTaskUnreadNotificationsByUser(int $taskId, User $user): Collection
    {
        return $user->unreadNotifications->filter(function(DatabaseNotification $notification) use($taskId) {
            return array_key_exists('task_id', $notification->data) && $notification->data['task_id'] === $taskId;
        });
    }

    /**
     * @return mixed
     */
    public function getTaskHaveOverDeadline(): Collection
    {
        return $this->taskRepo->getTaskHaveOverDeadline();
    }

    /**
     * @return mixed
     */
    public function getTaskHaveOverBudget()
    {
        return $this->taskRepo->getTaskHaveOverBudget();
    }
}
