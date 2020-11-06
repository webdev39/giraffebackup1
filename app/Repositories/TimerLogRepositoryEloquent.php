<?php

namespace App\Repositories;

use App\Models\TimerLog;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TimerLogRepositoryEloquent extends BaseRepositoryEloquent
{
    public const FILTERS = [
        'files',
        'timer_logs'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TimerLog::class;
    }

    /**
     * @return Builder|static
     */
    public function buildLogWithRelations()
    {
        return DB::table($this->logTable)
            ->select([
                $this->logTable.'.id                        as id',
                $this->timerTable.'.comment                 as body',
                $this->timerTable.'.comment                 as field',
                $this->logTable.'.updated_at                as updated_at',
                $this->logTable.'.created_at                as created_at',
                $this->userTenantTable.'.user_id            as user_id',
                $this->userTenantTable.'.id                 as user_tenant_id',
                $this->taskTable.'.id                       as task_id',
                $this->taskTable.'.name                     as task_name',
                $this->boardTable.'.id                      as board_id',
                $this->boardTable.'.name                    as board_name',
                $this->groupTable.'.id                      as group_id',
                $this->groupTable.'.name                    as group_name',
            ])
            ->leftJoin($this->timerLogTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->logTable.'.id',$this->timerLogTable.'.log_id');
            })
            ->leftJoin($this->timerTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->timerTable.'.id',$this->timerLogTable.'.timer_id');
            })
            ->leftJoin($this->userTenantTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTenantTable.'.id',$this->timerTable.'.user_tenant_id');
            })
            ->leftJoin($this->taskTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->taskTable.'.id',$this->timerTable.'.task_id');
            })
            ->leftJoin($this->boardTaskTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->boardTaskTable.'.task_id',$this->timerTable.'.task_id');
            })
            ->leftJoin($this->boardTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->boardTable.'.id',$this->boardTaskTable.'.board_id');
            })
            ->leftJoin($this->groupTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->groupTable.'.id',$this->boardTable.'.group_id');
            });
    }

    /**
     * @param int  $groupId
     * @param null $date
     *
     * @return Collection
     */
    public function getTimerLogByGroupId(int $groupId, $date = null) : Collection
    {
        $query = $this->buildLogWithRelations()
            ->where($this->groupTable.'.id', $groupId);

        if ($date) {
            return $query->whereDate($this->logTable.'.created_at', (new Carbon($date))->toDateString())->get();
        }

        return $query->get();
    }

    /**
     * @param int  $userTenantId
     * @param null $date
     *
     * @return Collection
     */
    public function getTimerLogByUserTenantId(int $userTenantId, $date = null) : Collection
    {
        $query = $this->buildLogWithRelations()
            ->where($this->timerTable.'.user_tenant_id', $userTenantId);

        if ($date) {
            return $query->whereDate($this->logTable.'.created_at', (new Carbon($date))->toDateString())->get();
        }

        return $query->get();
    }

    /**
     * @param array $logIds
     *
     * @return Collection
     */
    public function getTimerLogByIds(array $logIds) : Collection
    {
        return $this->buildLogWithRelations()
            ->whereIn($this->logTable.'.id', $logIds)
            ->get();
    }

    /**
     * @return Collection
     */
    public function getTimerLog() : Collection
    {
        return $this->buildLogWithRelations()
            ->get();
    }

    /**
     * @param array $timerIds
     *
     * @return Collection
     */
    public function getTimerLogByTimerIds(array $timerIds) : Collection
    {
        return $this->buildLogWithRelations()
            ->whereIn($this->timerTable.'.id', $timerIds)
            ->get();
    }

    /**
     * @param int       $taskId
     * @param int|null  $userTenantId
     * @param bool|null $onlyMyLogs
     *
     * @return Collection
     */
    public function getTimerLogByTaskId(int $taskId, int $userTenantId = null, bool $onlyMyLogs = null)
    {
        $build = $this->buildLogWithRelations()
            ->where($this->timerTable.'.task_id', $taskId);

        if (!is_null($userTenantId)) {
            $build->where($this->timerTable.'.user_tenant_id', $userTenantId);
        }

        if (!is_null($onlyMyLogs)) {
            if ($onlyMyLogs) {
                $build->where($this->userTenantTable.'.user_id', \Auth::id());
            } else {
                $build->where($this->userTenantTable.'.user_id', '!=', \Auth::id());
            }
        }

        return $build->get();
    }

    public function getTimerLogData(array $timerLogIds) : Collection
    {
        return app('LogRepo')->with(['timer.task.board', 'timer.userTenant.user', 'attachments', 'attachments.user'])->findWhereIn('id', $timerLogIds);
    }

    public function getTaskTimerLogIds(int $taskId) : Collection
    {

        $timerTable    = app('TimerRepo')->model->getTable();
        $timerLogTable = app('TimerLogRepo')->model->getTable();
        $logTable      = app('LogRepo')->model->getTable();

        return DB::table($timerTable)
            ->select(
                $logTable . '.id',
                $timerTable . '.user_tenant_id'
            )
            ->join(
                $timerLogTable,
                $timerTable . '.id',
                '=',
                $timerLogTable . '.timer_id'
            )
            ->join(
                $logTable,
                $timerLogTable . '.log_id',
                '=',
                $logTable . '.id'
            )
            ->where($timerTable . '.task_id', $taskId)
            ->get();
    }

    /**
     * @param string $table
     * @param int $id
     * @param array $filters
     * @param array $range
     * @param array $users
     * @param array $assigned
     * @param array $createdBy
     * @param array $subscribed
     * @param bool|null $close
     * @return Builder
     */
    public function getFilteredTimerLogs(
        string $table,
        int $id,
        array $filters,
        array $range,
        array $users,
        array $assigned,
        array $createdBy,
        array $subscribed,
        bool $close = null
    ): Builder
    {
        $timerLogs = $this->buildLogWithRelations()
            ->addSelect([
                DB::raw("null as parent_id"),
                DB::raw("'timer_log' as source"),
            ])
            ->where($table . '.id', $id);


        if (!is_null($close)) {
            $operator = $close ? '!=' : '=';
            $timerLogs = $timerLogs->where($this->taskTable . '.done_by', $operator, null);
        }

        if (in_array('files', static::FILTERS, false) && !in_array('timer_logs', static::FILTERS, false)) {
            $timerLogs = $timerLogs->leftJoin($this->logAttachmentTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->logAttachmentTable . '.log_id', $this->logTable . '.id');
            })->whereNotNull($this->logAttachmentTable . '.log_id');
        }

        if (count($range) === 2) {
            $timerLogs = $timerLogs->whereBetween($this->logTable . '.created_at', [
                Carbon::parse($range[0]),
                Carbon::parse($range[1])
            ]);
        }

        if (count($users) > 0) {
            $timerLogs = $timerLogs->whereIn($this->userTenantTable . '.user_id', $users);
        }

        if (count($assigned) > 0) {
            $timerLogs = $timerLogs
                ->leftJoin($this->userTenantTaskTable, function ($join) {
                    /** @var JoinClause $join */
                    $join->on($this->userTenantTaskTable . '.task_id', $this->taskTable . '.id');
                })
                ->whereIn($this->userTenantTaskTable . '.user_tenant_id', $assigned);
        }

        if (count($createdBy) > 0) {
            $timerLogs = $timerLogs->whereIn($this->userTenantTable. '.user_id', $createdBy);
        }

        if (count($subscribed) > 0) {
            $timerLogs = $timerLogs
                ->join($this->notificationSubscriptionTable, function ($join) {
                    /** @var JoinClause $join */
                    $join->on($this->notificationSubscriptionTable . '.task_id', $this->taskTable . '.id');
                })
                ->whereIn($this->notificationSubscriptionTable . '.user_id', $subscribed);
        }

        return $timerLogs;
    }
}
