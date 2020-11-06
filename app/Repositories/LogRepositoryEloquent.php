<?php

namespace App\Repositories;

use App\Models\Log;
use App\Models\Timer;
use App\Services\Time\TimeService;
use Carbon\CarbonInterval;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class LogRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Log::class;
    }

    /**
     * @param array    $attributes
     * @param int|null $logId
     *
     * @return $this|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createOrUpdateLog(array $attributes, int $logId = null)
    {
        $attributes['body'] = self::createLogMessage(TimeService::stringToCarbonInterval($attributes['time']), $attributes['user_tenant_id']);

        $log = $this->updateOrCreate(['id' => $logId], $attributes);

        if (isset($attributes['timer_id'])) {
            app('TimerLogRepo')->updateOrCreate([
                'timer_id' => $attributes['timer_id'],
                'log_id'   => $log->id
            ]);
        }

        return $log;
    }

    /**
     * @param Timer    $timer
     * @param int|null $logId
     *
     * @return LogRepositoryEloquent|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createOrUpdateLogFromTimer(Timer $timer, int $logId = null)
    {
        $time = TimeService::getSumTimeByTimer($timer);

        return $this->createOrUpdateLog([
            'time'              => "{$time->h}:{$time->i}:{$time->s}",
            'timer_id'          => $timer->id,
            'user_tenant_id'    => $timer->user_tenant_id,
        ], $logId);
    }

    /**
     * @param CarbonInterval $time
     * @param int            $userTenantId
     * @param string         $message
     *
     * @return string
     */
    private static function createLogMessage(CarbonInterval $time, int $userTenantId, $message = '')
    {
        $user = app('UserTenantRepo')->find($userTenantId)->user;

        if ($time->h) {
            $message.= $time->h.' h ';
        }

        if ($time->i) {
            $message.= $time->i.' m ';
        }

        if ($time->s) {
            $message.= $time->s.' s ';
        }

        if ($message) {
            $message = $user->full_name.' logged '.trim($message);
        }

        return $message;
    }

    /**
     * @return mixed
     */
    public function getLogsWithoutTimers()
    {
        return DB::table($this->logTable)
            ->select([
                $this->logTable.'.*',
            ])
            ->leftJoin($this->timerLogTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->timerLogTable.'.log_id', $this->logTable.'.id');
            })
            ->whereNull($this->timerLogTable.'.id')
            ->get();
    }
}
