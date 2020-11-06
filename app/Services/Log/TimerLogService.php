<?php

namespace App\Services\Log;

use App\Collections\LogCollection;
use App\Collections\TimerCollection;
use App\Collections\UserCollection;
use App\Events\CreatedCommentEvent;
use App\Models\Log;
use App\Models\Timer;
use App\Models\User;
use App\Models\UserTenant;
use App\Repositories\LogRepositoryEloquent;
use App\Repositories\TimerRepositoryEloquent;
use App\Services\BaseService;
use App\Services\Timer\TimerService;
use App\Services\Time\TimeService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Exceptions\ValidatorException;

class TimerLogService extends BaseService
{
    /** @var TimerService */
    private $timerService;

    /** @var LogRepositoryEloquent */
    private $logRepo;

    /** @var TimerRepositoryEloquent */
    private $timerRepo;

    /** @var TimerRepositoryEloquent */
    private $timerLogRepo;

    /**
     * TimerController constructor.
     */
    public function __construct()
    {
        $this->timerService     = app('TimerSer');

        $this->logRepo          = app('LogRepo');
        $this->timerRepo        = app('TimerRepo');
        $this->timerLogRepo     = app('TimerLogRepo');
    }

    public function getTimerLogListForUser(User $user, $date): Collection
    {
        return $this->getTimerLogListByUserTenantId($user->getChosenTenant()->id, $date);
        //TODO: waiting for more info
        if(!$user->able('TIME_TRACKING')) {
            return $this->getTimerLogListByUserTenantId($user->getChosenTenant()->id, $date);
        }

        $logs = collect([]);
        foreach($user->getChosenTenant()->userTenantGroups as $userTenantGroup) {
            if($userTenantGroup->able('READ_TIME_LOGS')) {
                $logs = $logs->merge($this->getTimerLogListByGroupId($userTenantGroup->group_id, $date));
            }
        }

        return $logs;
    }

    /**
     * @param int $groupId
     * @param $date
     * @return Collection
     */
    public function getTimerLogListByGroupId(int $groupId, $date) : Collection
    {
        $logs = $this->timerLogRepo->getTimerLogByGroupId($groupId, $date);
        $logs = $this->addTimerLogRelations($logs);

        return $logs;
    }

    /**
     * @param int $userTenantId
     * @param     $date
     *
     * @return Collection
     */
    public function getTimerLogListByUserTenantId(int $userTenantId, $date) : Collection
    {
        $logs = $this->timerLogRepo->getTimerLogByUserTenantId($userTenantId, $date);
        $logs = $this->addTimerLogRelations($logs);

        return $logs;
    }

    /**
     * @param int $logId
     *
     * @return mixed
     */
    public function getTimerLogByIdWithRelations(int $logId)
    {
        $logs = $this->timerLogRepo->getTimerLogByIds([$logId]);
        $logs = $this->addTimerLogRelations($logs);

        return $logs->first();
    }

    /**
     * @param int $timerId
     *
     * @return mixed
     */
    public function getTimerLogByTimerIdWithRelations(int $timerId)
    {
        $logs = $this->timerLogRepo->getTimerLogByTimerIds([$timerId]);
        $logs = $this->addTimerLogRelations($logs);

        return $logs->first();
    }

    /**
     * @param int       $taskId
     * @param int|null  $userTenantId
     * @param bool|null $onlyMyLogs
     *
     * @return TimerLogService|Collection
     */
    public function getTimerLogByTaskIdWithRelations(int $taskId, int $userTenantId = null, bool $onlyMyLogs = null)
    {
        $logs = $this->timerLogRepo->getTimerLogByTaskId($taskId, $userTenantId, $onlyMyLogs);
        $logs = $this->addTimerLogRelations($logs);

        return $logs;
    }

    /**
     * @return TimerLogService|Collection|mixed
     */
    public function getTimerLog()
    {
        $logs = $this->timerLogRepo->getTimerLog();
        $logs = $this->addTimerLogRelations($logs);

        return $logs;
    }

    /**
     * @param Collection $logs
     *
     * @return Collection|static
     */
    public function addTimerLogRelations(Collection $logs) : Collection
    {
        $logIds         = $logs->pluck('id')->unique()->toArray();
        $userIds        = $logs->pluck('user_id')->unique()->toArray();

        $timers         = $this->timerRepo->getTimersByLogIds($logIds);
        $timerIds       = $timers->pluck('id')->unique()->toArray();

        $attachments    = app('AttachmentRepo')->getAttachmentIdsByLogIds($logIds);
        $pauses         = app('PauseRepo')->getPausesByTimerIds($timerIds);
        $users          = app('UserRepo')->getUsersByIds($userIds);

        $timers = TimerCollection::make($timers);
        $timers->setAttributes([
            'pauses' => $pauses->groupBy('timer_id'),
        ]);

        $logs = LogCollection::make($logs);
        $logs->setAttributes([
            'timer' => $timers->groupBy('log_id'),
        ],true);

        $logs->setAttributes([
            'attachments' => $attachments->groupBy('log_id'),
        ]);

        $users = UserCollection::make($users);
        $logs->setUser($users);

        return $logs;
    }

    /**
     * @param array    $attributes
     * @param int|null $logId
     *
     * @return LogRepositoryEloquent|mixed
     * @throws ValidatorException|\Exception
     */
    public function createOrUpdateTimerLog(array $attributes, int $logId = null)
    {
        if ($logId) {
            $log    = $this->logRepo->find($logId);
            $timer  = $this->timerService->createOrUpdateTimer($attributes, $log->timer_id);
        } else {
            $timer  = $this->timerService->createOrUpdateTimer($attributes);
        }

        if ($attributes['time']) {
            app('TimerSer')->deletePausesByTimerId($timer->id);
            app('BillingSer')->updateOrCreateTimerBilling($timer);
        }

        $log = $this->logRepo->createOrUpdateLogFromTimer($timer, $logId);

        if (isset($attributes['attachments_id'])) {
            $this->updateLogAttachmentLinks($log->id, $attributes['attachments_id']);
        }

        if (request()->get('taskId') && request()->get('mentions')) {
            $task = app('TaskSer')->getTaskById(request()->get('taskId'));

            if (request()->get('mentions')) {
                event(new CreatedCommentEvent(Auth::user(), request()->get('mentions'), $attributes['comment'], $task->group_id, $task));
            }
        }

        return $log;
    }

    /**
     * @param Timer    $timer
     * @param int|null $logId
     *
     * @return LogRepositoryEloquent|mixed
     * @throws ValidatorException
     */
    public function createOrUpdateLogFromTimer(Timer $timer, int $logId = null)
    {
        return $this->logRepo->createOrUpdateLogFromTimer($timer, $logId);
    }

    /**
     * @param $logId
     *
     * @throws \Throwable
     */
    public function removeLog($logId)
    {
        $log    = $this->logRepo->find($logId);
        $timer  = $log->timer()->first();

        DB::transaction(function () use ($log, $timer) {
            $this->logRepo->delete($log->id);

            if ($timer) {
                $this->timerRepo->delete($timer->id);
            }
        });
    }

    /**
     * @param int $logId
     * @param     $attachmentIds
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateLogAttachmentLinks(int $logId, $attachmentIds = [])
    {
        $logAttachmentIds = $this->logRepo->find($logId)->attachments->pluck('id')->all();

        foreach (array_diff($attachmentIds, $logAttachmentIds) as $attachmentId) {
            $this->createLogAttachmentLink($logId, $attachmentId);
        }

        foreach (array_diff($logAttachmentIds, $attachmentIds) as $attachmentId) {
            $this->removeLogAttachmentLink($logId, $attachmentId);
        }
    }

    /**
     * @param int $logId
     * @param     $attachmentIds
     *
     * @throws ValidatorException
     */
    public function createLogAttachmentLink(int $logId, $attachmentIds)
    {
        app('LogAttachmentRepo')->create([
            'log_id'        => $logId,
            'attachment_id' => $attachmentIds
        ]);
    }

    /**
     * @param int $logId
     * @param     $attachmentIds
     */
    public function removeLogAttachmentLink(int $logId, $attachmentIds)
    {
        app('LogAttachmentRepo')->deleteWhere([
            'log_id'        => $logId,
            'attachment_id' => $attachmentIds
        ]);
    }











    /**
     * @param array $timerLogIds
     *
     * @return Collection
     */
    public function getTimerLogData(array $timerLogIds) : Collection
    {
        return app('LogRepo')->with(['timer.task.board', 'timer.userTenant.user', 'attachments', 'attachments.user'])->findWhereIn('id', $timerLogIds);
    }

    /**
     * If user has no permission to read logs from other members, it is necessary to add the third parameter - $isOwn
     * (bool)
     *
     * @param int $taskId
     * @param UserTenant $userTenant
     * @return mixed
     */
    public function getTaskTimerLogs(int $taskId, UserTenant $userTenant, $isOwn)
    {
        /** @var Collection $logs */
        $logs = $this->getTimerLogData(app('TimerLogRepo')->getTaskTimerLogIds($taskId)->pluck('id')->all());

        $logs = $logs->filter(function($log) use ($userTenant, $isOwn) {
            return $isOwn ? $log->timer->first()->user_tenant_id === $userTenant->id : true ;
        });

        return $logs;
    }

    /**
     * @param array $timerLogIds
     *
     * @return Collection
     */
    public function getCompleteTimerLogData(array $timerLogIds) : Collection
    {
        return app('LogRepo')->with(['timer.task.board', 'timer.userTenant.user'])->findWhereIn('id', $timerLogIds);
    }

    /**
     * @param $logId
     *
     * @return Log
     */
    public function getLogById($logId) :Log
    {
        return app('LogRepo')->find($logId);
    }
}
