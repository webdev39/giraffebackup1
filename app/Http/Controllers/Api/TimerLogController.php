<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateTimerLogRequest;
use App\Http\Requests\MainLogListRequest;
use App\Http\Requests\UpdateTimerLogRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\LogResource;
use App\Models\Log;
use App\Models\Task;
use App\Models\Timer;
use App\Services\Log\TimerLogService;
use App\Services\Task\TaskService;
use App\Services\Timer\TimerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class TimerLogController
 *
 * @package App\Http\Controllers\Api
 */
class TimerLogController extends Controller
{
    /** @var TaskService */
    private $taskService;

    /** @var TimerService  */
    private $timerService;

    /** @var TimerLogService */
    private $timerLogService;

    /**
     * TimerLogController constructor.
     */
    public function __construct()
    {
        $this->taskService      = app('TaskSer');
        $this->timerService     = app('TimerSer');
        $this->timerLogService  = app('TimerLogSer');
    }

    /**
     * @param MainLogListRequest $request
     *
     * @return JsonResponse
     */
    public function index(MainLogListRequest $request)
    {
        $logs = $this->timerLogService->getTimerLogListForUser(Auth::user(), $request->get('date'));

        return response()->json([
            'message'   => 'Tenant timer logs',
            'logs'      => LogResource::collection($logs)
        ]);
    }

    /**
     * @param Task $task
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Task $task)
    {
        $this->authorize('readTimeLogs', [Log::class, $task]);

        $logs = $this->timerLogService->getTimerLogByTaskIdWithRelations($task->id, Auth::userTenantId(), request('onlyMyLogs', null));

        return response()->json([
            'message' => 'Timer logs for task details',
            'logs'      => LogResource::collection($logs)
        ]);
    }

    /**
     * @param CreateTimerLogRequest $request
     *
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function create(CreateTimerLogRequest $request)
    {
        self::failIfUserHasNotLogCreatePermission();

        $log = $this->timerLogService->createOrUpdateTimerLog([
            'attachments_id' => $request->get('attachments'),
            'task_id'        => $request->get('taskId'),
            'comment'        => $request->get('comment'),
            'time'           => $request->get('time'),
            'logged_at'      => $request->get('logDate'),
            'user_tenant_id' => Auth::userTenantId(),
        ]);

        if ($request->get('start')) {
            $this->timerService->pauseActiveTimerByUserTenantId(Auth::userTenantId());
            $this->timerService->startTimer($log->timer->first()->id);
        }

        $log = $this->timerLogService->getTimerLogByIdWithRelations($log->id);

        return response()->json([
            'message'   => 'Manually timer has been created',
            'log'       => new LogResource($log),
        ]);
    }

    /**
     * @param UpdateTimerLogRequest $request
     * @param Log                   $log
     *
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function update(UpdateTimerLogRequest $request, Log $log)
    {
        $timer = $log->timer()->first();

        self::failIfUserHasNotLogUpdatePermission($log, $timer);

        $log = $this->timerLogService->createOrUpdateTimerLog([
            'attachments_id' => $request->get('attachments'),
            'task_id'        => $request->get('taskId'),
            'comment'        => (string) $request->get('comment'),
            'time'           => $request->get('time'),
            'logged_at'      => $request->get('logDate'),
            'user_tenant_id' => $timer->user_tenant_id,
        ], $log->id);

        app('BillingSer')->updateOrCreateTimerBilling($timer->id);
        
        $log = $this->timerLogService->getTimerLogByIdWithRelations($log->id);

        return response()->json([
            'message'   => 'Manually timer has been updated',
            'log'       => new LogResource($log),
        ]);
    }


    /**
     * @param Log $log
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function destroy(Log $log)
    {
        $timer = $log->timer->first();

        self::failIfUserHasNotLogRemovePermission($log, $timer);

        $this->timerLogService->removeLog($log->id);

        return response()->json([
            'message' => 'Timer Log has been removed'
        ]);
    }

    /**
     *
     */
    private static function failIfUserHasNotLogCreatePermission()
    {
        if (request()->get('taskId')) {
            $task = app('TaskSer')->getTaskById(request()->get('taskId'));

            if (!Auth::user()->can('create', [Log::class, $task])) {
                abort('You don\'t have the time tracking permission in this group', 403);
            }
        }
    }

    /**
     * @param Log   $log
     * @param Timer $timer
     */
    private static function failIfUserHasNotLogUpdatePermission(Log $log, Timer $timer)
    {
        $canTimeTrack = Auth::user()->can('timeTrack', $log) || $timer->user_tenant_id === Auth::userTenantId();

        if (!Auth::user()->can('updateLog', $log) && !$canTimeTrack) {
            abort('You don\'t have permission to edit time logs', 403);
        }
    }

    /**
     * @param Log   $log
     * @param Timer $timer
     */
    private static function failIfUserHasNotLogRemovePermission(Log $log, Timer $timer)
    {
        $canTimeTrack = Auth::user()->can('timeTrack', $log) || $timer->user_tenant_id === Auth::userTenantId();

        if (!Auth::user()->can('removeLog', $log) && !$canTimeTrack) {
            abort('You don\'t have permission to delete time logs', 403);
        }
    }
}
