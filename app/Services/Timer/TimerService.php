<?php

namespace App\Services\Timer;

use App\Collections\TimerCollection;
use App\Models\BillingStatus;
use App\Models\Budget;
use App\Models\Timer;
use App\Models\UserTenant;
use App\Repositories\PauseRepositoryEloquent;
use App\Repositories\TimerRepositoryEloquent;
use App\Services\BaseService;
use App\Services\Task\TaskService;
use App\Services\Time\TimeService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Exceptions\ValidatorException;

class TimerService extends BaseService
{
    /**
     * @var TimerRepositoryEloquent
     */
    private $timerRepo;

    /**
     * @var PauseRepositoryEloquent
     */
    private $pauseRepo;

    /**
     * TimerService constructor.
     */
    public function __construct()
    {
        $this->timerRepo = app('TimerRepo');
        $this->pauseRepo = app('PauseRepo');
    }

    /**
     * @param int $userTenantId
     *
     * @return Collection
     */
    public function getTimerListByUserTenantId(int $userTenantId): Collection
    {
        $timers = $this->timerRepo->getTimersByUserTenantId($userTenantId);
        $timerIds = $timers->pluck('id')->toArray();

        $pauses = $this->pauseRepo->getPausesByTimerIds($timerIds)->groupBy('timer_id');

        $timers = TimerCollection::make($timers);
        $timers->setAttributes([
            'pauses' => $pauses
        ]);

        $timers->setTrackedTime($pauses);

        return $timers->sortBy('end_time')->values();
    }

    /**
     * @param array $taskIds
     *
     * @return float|null
     * @throws \Exception
     */
    public function getTaskSpentTime(array $taskIds)
    {
        return self::calcTaskSpentTime($this->getTimersByTaskId($taskIds));
    }

    /**
     * @param $taskId
     *
     * @return Collection
     */
    public function getTimersByTaskId($taskId): Collection
    {
        if (is_array($taskId)) {
            if (count($taskId) > 0) {
                return $this->timerRepo->findWhereIn('task_id', $taskId);
            }

            return new Collection();
        }

        return $this->timerRepo->findWhere(['task_id' => $taskId]);
    }

    /**
     * @param Collection $timers
     *
     * @return float|null
     * @throws \Exception
     */
    public static function calcTaskSpentTime(Collection $timers)
    {
        if ($timers->isEmpty()) {
            return 0;
        }

        $timeStart = now();
        $timeEnd = now();

        $timers->map(function ($timer) use ($timeStart) {
            if (!is_null($timer->end_time)) {
                $timeStart->add(TimeService::getSumTimeByTimer($timer));
            }
        });

        return TimeService::getTotalMinutes($timeStart->diff($timeEnd));
    }

    /**
     * @param Collection $timers
     *
     * @return float|null
     * @throws \Exception
     */
    public static function calcTaskBilledTime(Collection $timers)
    {
        if ($timers->isEmpty()) {
            return 0;
        }

        $timeStart = now();
        $timeEnd = now();

        $timers->map(function ($timer) use ($timeStart) {
            if (BillingStatus::INITIAL_STATUSES['Billed']['id'] === $timer->billing_status_id) {
                $timeStart->add(TimeService::getSumTimeByTimer($timer));
            }
        });

        return TimeService::getTotalMinutes($timeStart->diff($timeEnd));
    }

    /**
     * @param array $attributes
     * @param int|null $timerId
     *
     * @return mixed
     * @throws ValidatorException
     */
    public function createOrUpdateTimer(array $attributes, int $timerId = null)
    {
        if (!isset($attributes['logged_at']) || is_null($attributes['logged_at'])) {
            $attributes['logged_at'] = now();
        }

        if (isset($attributes['time'])) {
            $attributes['start_time'] = TimeService::addTimeToDate($attributes['logged_at'], $attributes['time']);
            $attributes['end_time'] = TimeService::addTimeToDate($attributes['logged_at']);
        }

        return $this->timerRepo->updateOrCreate(['id' => $timerId], $attributes);
    }

    /**
     * @param int $timerId
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function getTimerByIdWithRelations(int $timerId)
    {
        if ($timer = $this->timerRepo->getTimerByIdWithRelations($timerId)) {
            $timer->pauses = $this->pauseRepo->findWhere(['timer_id' => $timer->id]);
            $timer->time = TimeService::getSumTimeByTimer($timer);
        }

        return $timer;
    }

    /**
     * Get current active timer for the user tenant
     *
     * @param int $userTenantId
     *
     * @return mixed
     */
    public function getCurrentActiveTimerByUserTenantId(int $userTenantId)
    {
        $timers = $this->timerRepo->getActiveOrPauseTimersByUserTenantId($userTenantId);
        $timerIds = $timers->pluck('id')->toArray();;

        $pauses = $this->pauseRepo->getPausesByTimerIds($timerIds)->groupBy('timer_id');

        $timers = TimerCollection::make($timers);
        $timers->setAttributes([
            'pauses' => $pauses
        ]);

        return $timers->getActiveTimer();
    }

    /**
     * @param int $userTenantId
     *
     * @return mixed|null
     * @throws ValidatorException
     */
    public function pauseActiveTimerByUserTenantId(int $userTenantId)
    {
        $timer = $this->getCurrentActiveTimerByUserTenantId($userTenantId);

        if ($timer) {
            return $this->pauseTimer($timer->id);
        }

        return null;
    }

    /**
     * @param int $timerId
     *
     * @return mixed
     * @throws ValidatorException
     */
    public function pauseTimer(int $timerId)
    {
        $timer = $this->getTimerById($timerId);
        $timer->touch();

        return $this->pauseRepo->create([
            'start_time' => self::getCarbonTimestamp(),
            'timer_id' => $timerId
        ]);
    }

    /**
     * @param int $timerId
     * @param int $pauseId
     *
     * @return mixed
     * @throws ValidatorException
     */
    public function continueTimer(int $timerId, int $pauseId)
    {
        $timer = $this->getTimerById($timerId);
        $timer->touch();

        return $this->pauseRepo->update([
            'end_time' => self::getCarbonTimestamp()
        ], $pauseId);
    }


    /**
     * @param int $timerId
     * @param string|null $time
     * @param string|null $loggedAt
     *
     * @return mixed
     * @throws ValidatorException
     */
    public function createPauseForTimer(int $timerId, string $time = null, string $loggedAt = null)
    {
        $timer = $this->getTimerById($timerId);
        $timer->touch();

        return $this->pauseRepo->create([
            'timer_id' => $timerId,
            'start_time' => TimeService::addTimeToDate($loggedAt, $time),
            'end_time' => TimeService::addTimeToDate($loggedAt),
        ]);
    }

    /**
     * @param null|Carbon $time
     *
     * @return string
     */
    public static function getCarbonTimestamp($time = null): string
    {
        if (!$time instanceof Carbon) {
            $time = Carbon::now();
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $time)->toDateTimeString();
    }

    /**
     * @param UserTenant $userTenant
     * @param int $timerId
     *
     * @return Timer|null
     */
    public function getTimerByUserTenantTimerId(UserTenant $userTenant, int $timerId): ?Timer
    {
        return $userTenant->timers()->where('id', $timerId)->first();
    }

    /**
     * @param array $attributes
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createTimer(array $attributes)
    {
        return $this->timerRepo->create($attributes);
    }

    /**
     * @param array $attributes
     * @param int $timerId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateTimer(array $attributes, int $timerId)
    {
        return $this->timerRepo->update($attributes, $timerId);
    }

    /**
     * @param int $timerId
     *
     * @return int
     */
    public function destroyTimer(int $timerId)
    {
        return $this->timerRepo->delete($timerId);
    }

    /**
     * @param int $timerId
     *
     * @return mixed
     * @throws ValidatorException
     */
    public function startTimer(int $timerId)
    {
        return $this->timerRepo->update([
            'start_time' => self::getCarbonTimestamp(),
            'end_time' => null
        ], $timerId);
    }

    /**
     * @param Collection $tasks
     *
     * @return float|null
     * @throws \Exception
     */
    public static function getTrackedTimeByTasks(Collection $tasks)
    {
        $timers = $tasks->pluck('timers')->collapse();

        return TimerService::calcTaskSpentTime($timers);
    }

    /**
     * @param int $timerId
     *
     * @return mixed
     */
    public function getTimerById(int $timerId)
    {
        return $this->timerRepo->find($timerId)->first();
    }

    /**
     * @param $timerId
     *
     * @return bool
     * @throws \Throwable
     */
    public function stopTimer($timerId)
    {
        return $this->stopTimers([$timerId]);
    }

    /**
     * @param array $timersIds
     *
     * @return bool|null
     * @throws \Exception
     */
    public function deletePausesByTimerIds(array $timersIds)
    {
        return $this->pauseRepo->deletePausesByTimerIds($timersIds);
    }

    /**
     * @param int $timerId
     *
     * @return bool|null
     * @throws \Exception
     */
    public function deletePausesByTimerId(int $timerId)
    {
        return $this->deletePausesByTimerIds([$timerId]);
    }

    /**
     * @param array $timerIds
     *
     * @return bool
     * @throws \Throwable
     */
    public function stopTimers(array $timerIds)
    {
        DB::transaction(function () use ($timerIds) {
            $endTime = self::getCarbonTimestamp();

            $this->timerRepo->stopTimerByIds($timerIds, $endTime);
            $this->pauseRepo->stopPauseByTimerIds($timerIds, $endTime);
        });

        return true;
    }


    public function getTimerByUserTenantTaskId(UserTenant $userTenant, int $taskId)
    {
        return $userTenant->timers->where('task_id', $taskId);
    }


    public function getTimerLog($timer)
    {
        if ($timer->end_time !== null) {
            $logs = [];
            $start = Carbon::parse($timer->start_time);
            $end = Carbon::parse($timer->end_time);
            $logs[]['start'] = $start;
            for ($i = 0; $i < count($timer->timerPauses); $i++) {
                $startPause = Carbon::parse($timer->timerPauses[$i]->start_time);
                $endPause = Carbon::parse($timer->timerPauses[$i]->end_time);
                $logs[$i]['end'] = $startPause;
                $logs[$i + 1]['start'] = $endPause;
            }
            $logs[count($logs) - 1]['end'] = $end;
            for ($i = 0; $i < count($logs); $i++) {
                $logs[$i]['time'] = $logs[$i]['start']->diff($logs[$i]['end']);
            }
            return $logs;
        }
        return false;
    }

    public function getTimersOfUserTenantByTaskId(int $userTenantId, int $taskId)
    {
        //$retColl = app('TimerRepo')->getTimersOfUserTenant($userTenant)->where('task_id', '=', $taskId);
        $retColl = app('TimerRepo')->getTimersOfUserTenantByTaskId($userTenantId, $taskId);
        foreach ($retColl as $timerBase) {
            $timerBase->timerPauses = app('PauseRepo')->findWhere(['timer_id' => $timerBase->id]);
            $timerBase->time = TimeService::getSumTimeByTimer($timerBase);
        }
        return $retColl;
    }

    public function getLastTimersByUserTenant(UserTenant $userTenant, $maxCount)
    {
        $retColl = app('TimerRepo')->getTimersOfUserTenant($userTenant);
        $active = [];
        $other = [];
        $allCount = 0;
        foreach ($retColl as $timerBase) {
            $timerBase->timerPauses = app('PauseRepo')->findWhere(['timer_id' => $timerBase->id]);
            $timerBase->time = TimeService::getSumTimeByTimer($timerBase);
        }
        foreach ($retColl as $timerBase) {
            if ($timerBase->start_time !== null && $timerBase->end_time === null) {
                $active[] = $timerBase;
                $allCount++;
            }
            if ($allCount >= $maxCount) break;
        }
        if ($allCount < $maxCount) {
            foreach ($retColl as $timerBase) {
                if ($timerBase->start_time === null || $timerBase->end_time !== null) {
                    $other[] = $timerBase;
                    $allCount++;
                }
                if ($allCount >= $maxCount) break;
            }
        }
        return array_merge($active, $other);
    }

    public function getCurrentPauseByTimerId(int $timerId)
    {
        return app('PauseRepo')->getCurrentPauseByTimerId($timerId);
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function convertTimeToHours($time)
    {
        $hours = $time->h;
        if ($time->m || $time->d) {
            $hours += 24 * $time->days;
        }
        return $hours;
    }

    public function getReportsTimersSummary($canReadReportsFully, $id, $selectGroups, $selectBoards, $selectMembers, $selectClients)
    {
        return app('TimerRepo')->getReportsTimersSummary($canReadReportsFully, $id, $selectGroups, $selectBoards, $selectMembers, $selectClients);
    }

    /**
     * @param $canReadReportsFully
     * @param $userTenantId
     * @param $tenantId
     * @param $selectGroups
     * @param $selectBoards
     * @param $selectMembers
     * @param $selectClients
     *
     * @return array|null
     */
    public function getReportsTimers($canReadReportsFully, $userTenantId, $tenantId, $selectGroups, $selectBoards, $selectMembers, $selectClients)
    {
        return app('TimerRepo')->getReportsTimers($canReadReportsFully, $userTenantId, $tenantId, $selectGroups, $selectBoards, $selectMembers, $selectClients);
    }


    /**
     * @param Budget $taskBudget
     * @param Collection $timers
     * @return string
     * @throws \Exception
     */
    public function overBudget(Budget $taskBudget, Collection $timers): ?String
    {
        if (!$taskBudget->soft_budget || !$taskBudget->hard_budget) {
            return null;
        }
        $parseSoftBudget = explode(':', $taskBudget->soft_budget);
        $parseHardBudget = explode(':', $taskBudget->hard_budget);
        $softBudgetTimeHours = CarbonInterval::hours($parseSoftBudget[0])->minutes($parseSoftBudget[1])->total('hours');
        $hardBudgetTimeHours = CarbonInterval::hours($parseHardBudget[0])->minutes($parseHardBudget[1])->total('hours');
        $spentTimeInTask = CarbonInterval::minutes(self::calcTaskSpentTime($timers))->cascade()->total('hours');

        if ($softBudgetTimeHours > 0 || $hardBudgetTimeHours > 0) {

            if ($spentTimeInTask > $softBudgetTimeHours && $spentTimeInTask > $hardBudgetTimeHours) {
                return 'notifications.budget.over.both';
            } elseif ($spentTimeInTask > $softBudgetTimeHours) {
                return 'notifications.budget.over.soft_budget';
            } elseif ($spentTimeInTask > $hardBudgetTimeHours) {
                return 'notifications.budget.over.hard_budget';
            } else {
                return null;
            }
        }

        return null;
    }
}
