<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateTimerRequest;
use App\Http\Requests\StartTimerRequest;
use App\Http\Requests\UpdateTimerRequest;
use App\Http\Resources\LogResource;
use App\Http\Resources\TimerResource;
use App\Models\Permission;
use App\Models\UserTenant;
use App\Services\Log\TimerLogService;
use App\Services\Timer\TimerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TimerController
{
    /** @var TimerService */
    protected $timerService;

    /** @var TimerLogService */
    protected $timerLogService;

    /**
     * TimerController constructor.
     */
    public function __construct()
    {
        $this->timerService     = app('TimerSer');
        $this->timerLogService  = app('TimerLogSer');
    }

    /**
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        $this->failIfUserHasNotTimeTrackPermission();

        if($timers = $this->timerService->getTimerListByUserTenantId(Auth::userTenantId())) {
            return response()->json([
                'timers'  => TimerResource::collection($timers)
            ]);
        }

        return response()->json([
            'timers'  => []
        ]);
    }

    /**
     * Timers of user of the task
     *
     * @param int $taskId
     *
     * @return JsonResponse
     */
    public function showTaskTimer(int $taskId)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->failIfUserHasNotTimeTrackPermission();
        $this->failIfUserHasNotGroupPermissionByTaskId($taskId);

        $timersOfTask = $this->timerService->getTimerByUserTenantTaskId($userTenant, $taskId);

        if(!$timersOfTask) {
            abort('Timer by task not found', 404);
        }

        return response()->json([
            'message'   => 'Timer user tenant timer details',
            'timer'     => TimerResource::collection($timersOfTask)
        ]);
    }

    /**
     * @param int $timerId
     *
     * @return JsonResponse
     */
    public function show(int $timerId)
    {
        $this->failIfUserHasNotTimeTrackPermission();

        $timer = $this->timerService->getTimerByIdWithRelations($timerId);

        if(!$timer) {
            abort('Timer not found', 404);
        }

        return response()->json([
            'message' => 'Timer details',
            'timer'   => new TimerResource($timer)
        ]);
    }

    /**
     * @param CreateTimerRequest $request
     *
     * @return JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(CreateTimerRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->failIfUserHasNotTimeTrackPermission();
        $this->failIfUserHasNotGroupPermissionByTaskId($request->get('taskId'));

        $timer = $this->timerService->createTimer([
            'comment'           => $request->get('comment'),
            'task_id'           => $request->get('taskId'),
            'user_tenant_id'    => $userTenant->id
        ]);

        $timer = $this->timerService->getTimerByIdWithRelations($timer->id);

        return response()->json([
            'message' => 'Timer created successfully',
            'timer'   => new TimerResource($timer)
        ]);
    }

    /**
     * @param CreateTimerRequest $request
     *
     * @return JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createStart(CreateTimerRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->failIfUserHasNotTimeTrackPermission();
        $this->failIfUserHasNotGroupPermissionByTaskId($request->get('taskId'));

        $timer = $this->timerService->createTimer([
            'comment'           => $request->get('comment'),
            'task_id'           => $request->get('taskId'),
            'user_tenant_id'    => $userTenant->id
        ]);

        $this->timerService->pauseActiveTimerByUserTenantId($userTenant->id);
        $this->timerService->startTimer($timer->id);
        event('timer.start', $timer->id);

        $timer = $this->timerService->getTimerByIdWithRelations($timer->id);

        return response()->json([
            'message'           => 'Timer created successfully',
            'timer'             => new TimerResource($timer),
        ]);
    }

    /**
     * @param UpdateTimerRequest $request
     *
     * @return JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(UpdateTimerRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->failIfUserHasNotTimeTrackPermission();
        $this->failIfUserHasNotGroupPermissionByTaskId($request->get('taskId'));
        $this->failIfUserHasNotTimeUpdatePermission($userTenant, $request->get('timerId'));

        $timer = $this->timerService->updateTimer([
            'comment'           => $request->get('comment'),
            'task_id'           => $request->get('taskId'),
        ], $request->get('timerId'));

        event('timer.update', $timer->id);

        $timer = $this->timerService->getTimerByIdWithRelations($timer->id);

        return response()->json([
            'message'   => 'Timer is updated',
            'timer'     => new TimerResource($timer)
        ]);
    }

    /**
     * @param StartTimerRequest $request
     *
     * @return JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function start(StartTimerRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->failIfUserHasNotTimeTrackPermission();
        $this->failIfUserHasNotTimeUpdatePermission($userTenant, $request->get('timerId'));

        $timer = $this->timerService->getTimerByUserTenantTimerId($userTenant, $request->get('timerId'));

        if(!$timer) {
            abort('This timer doesn\'t exists or you\'re not owner!',404);
        }

        if($timer->start_time === null) {
            $this->timerService->pauseActiveTimerByUserTenantId($userTenant->id);

            $timer = $this->timerService->startTimer($request->get('timerId'));
            event('timer.start', $timer->id);
        }

        $timer = $this->timerService->getTimerByIdWithRelations($timer->id);

        return response()->json([
            'message'           => 'Timer is started',
            'timer'             => new TimerResource($timer),
        ]);
    }

    /**
     * @param StartTimerRequest $request
     *
     * @return JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Throwable
     */
    public function stop(StartTimerRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->failIfUserHasNotTimeTrackPermission();

        $timer = $this->timerService->getTimerByUserTenantTimerId($userTenant, $request->get('timerId'));

        if(!$timer) {
            abort('This timer doesn\'t exists or you\'re not owner!',404);
        }

        if($timer->end_time === null) {
            $this->timerService->stopTimer($timer->id);
            $this->timerLogService->createOrUpdateLogFromTimer($timer);

            app('BillingSer')->updateOrCreateTimerBilling($timer->id);

            event('timer.stop', $timer->id);
        }

        $log = $this->timerLogService->getTimerLogByTimerIdWithRelations($timer->id);

        return response()->json([
            'message'   => 'Timer is stopped',
            'log'       => new LogResource($log)
        ]);
    }

    /**
     * @param StartTimerRequest $request
     *
     * @return JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function pause(StartTimerRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->failIfUserHasNotTimeTrackPermission();

        $timer = $this->timerService->getTimerByUserTenantTimerId($userTenant, $request->get('timerId'));

        if (!$timer) {
            return response()->json(['message' => 'This timer doesn\'t exists or you\'re not owner!'], 404);
        }

        if ($timer['end_time'] !== null) {
            return response()->json(['message' => 'You cannot pause stopped timer'], 403);
        }

        $currentPause = $this->timerService->getCurrentPauseByTimerId($request->get('timerId'));

        if (!$currentPause) {
            $this->timerService->pauseTimer($request->get('timerId'));
            event('timer.pause', $request->get('timerId'));
        }

        $timer = $this->timerService->getTimerByIdWithRelations($request->get('timerId'));
        
        return response()->json([
            'message'   => 'Timer is paused',
            'timer'     => new TimerResource($timer)
        ]);
    }

    /**
     * @param StartTimerRequest $request
     *
     * @return JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function continue(StartTimerRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->failIfUserHasNotTimeTrackPermission();

        $timer = $this->timerService->getTimerByUserTenantTimerId($userTenant, $request->get('timerId'));

        if(!$timer) {
            return response()->json(['message' => 'This timer doesn\'t exists or you\'re not owner!'],404);
        }

        $currentPause = $this->timerService->getCurrentPauseByTimerId($request->get('timerId'));

        if(!$currentPause) {
            return response()->json(['message' => 'This timer doesn\'t paused!'],403);
        }

        $this->timerService->pauseActiveTimerByUserTenantId($userTenant->id);

        $this->timerService->continueTimer($timer->id, $currentPause->id);
        event('timer.continue', $timer->id);

        $timer = $this->timerService->getTimerByIdWithRelations($currentPause->timer_id);

        return response()->json([
            'message'           => 'Timer is continued',
            'timer'             => new TimerResource($timer),
        ]);
    }

    /**
     * @param StartTimerRequest $request
     *
     * @return JsonResponse
     */
    public function destroy(StartTimerRequest $request)
    {
        $this->failIfUserHasNotTimeTrackPermission();

        $this->timerService->destroyTimer($request->get('timerId'));

        return response()->json([
            'message' => 'Timer is deleted'
        ]);
    }

    /**
     * @param UserTenant $userTenant
     * @param int        $timerId
     */
    private function failIfUserHasNotTimeUpdatePermission(UserTenant $userTenant, int $timerId)
    {
        $timer = $this->timerService->getTimerByUserTenantTimerId($userTenant, $timerId);

        if(!$timer) {
            abort('This timer doesn\'t exists or you\'re not owner!',404);
        }
    }

    /**
     * @return bool|JsonResponse
     */
    private function failIfUserHasNotTimeTrackPermission()
    {
        return Auth::failIfHasNoPermission(Permission::TIME_TRACKING_PERMISSION);
    }

    /**
     * @param $taskId
     *
     * @return bool
     */
    private function failIfUserHasNotGroupPermissionByTaskId($taskId)
    {
        if($taskId === null) {
            return true;
        }

        $task = app('TaskSer')->getTaskById((int) $taskId);

        if (!$task) {
            abort(404, 'Task not found');
        }

        $board = app('BoardSer')->getBoardModelById($task->board_id);

        if (!$board) {
            abort(404, 'Board not found');
        }

        $userTenantGroup = Auth::userTenantGroup($board->group_id);

        if (!$userTenantGroup) {
            abort(403, 'User Tenant is not member of the group');
        }

        return true;
    }
}
