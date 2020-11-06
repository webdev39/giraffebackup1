<?php

namespace App\Repositories;

use App\Models\Board;
use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

/**
 * Class ActivityLogRepositoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ActivityLogRepositoryEloquent extends BaseRepositoryEloquent
{
    public const ACTIVITY_LOG_FILTERS = [
        'created',
        'opened',
        'closed',
        'assigned',
        'assigned_and_subscribed',
        'unassigned',
        'subscribed',
        'unsubscribed',
        'archived',
        'unarchived',
        'changed',
        'increased',
        'decreased',
        'deleted',
        'created',
        'subscribed',
        'unsubscribed'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Activity::class;
    }

    /**
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function buildActivityLogWithRelations()
    {
        return DB::table($this->activityLogTable)
            ->select([
                $this->activityLogTable.'.id            as id',
                $this->activityLogTable.'.field         as field',
                $this->activityLogTable.'.description   as body',
                $this->activityLogTable.'.updated_at    as updated_at',
                $this->activityLogTable.'.created_at    as created_at',
                $this->userTenantTable.'.user_id        as user_id',
                $this->userTenantTable.'.id             as user_tenant_id',
                $this->taskTable.'.id                   as task_id',
                $this->taskTable.'.name                 as task_name',
                $this->boardTable.'.id                  as board_id',
                $this->boardTable.'.name                as board_name',
                $this->groupTable.'.id                  as group_id',
                $this->groupTable.'.name                as group_name',
            ])
            ->leftJoin($this->userTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTable.'.id',$this->activityLogTable.'.causer_id');
            })
            ->leftJoin($this->userTenantTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTenantTable.'.user_id', $this->userTable.'.id');
                $join->on($this->userTenantTable.'.tenant_id', $this->userTable.'.chosen_tenant_id');
            })
            ->leftJoin($this->taskTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->taskTable.'.id',$this->activityLogTable.'.task_id');
            })
            ->leftJoin($this->boardTaskTable, $this->boardTaskTable . '.task_id', '=', $this->taskTable.'.id')
            ->leftJoin($this->boardTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->boardTable.'.id',$this->boardTaskTable.'.board_id');
            })
            ->leftJoin($this->groupTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->groupTable.'.id',$this->activityLogTable.'.group_id');
            });
    }

    /**
     * @param int $taskId
     * @param int $perPage
     *
     * @return mixed
     */
    public function getActivityLogByTaskId(int $taskId, $perPage = 30)
    {
        return $this->scopeQuery(function ($query) use ($taskId) {
            /** @var $query Builder */
            return $query
                ->where('log_name', (new Task())->logName)
                ->where('task_id', $taskId);
        })->paginate($perPage);
    }

    /**
     * @param int $boardId
     * @param int $perPage
     *
     * @return mixed
     */
    public function getActivityLogByBoardId(int $boardId, $perPage = 30)
    {
        return $this->scopeQuery(function ($query) use ($boardId) {
            /** @var $query Builder */
            return $query->where(function ($query) {
                    /** @var $query Builder */
                    $query->where('log_name', (new Task())->logName)
                        ->orWhere('log_name', (new Board())->logName);
                })
                ->where('board_id', $boardId);
        })->paginate($perPage);
    }

    /**
     * @param int $groupId
     * @param int $perPage
     *
     * @return mixed
     */
    public function getActivityLogByGroupId(int $groupId, $perPage = 30)
    {
        return $this->scopeQuery(function ($query) use ($groupId) {
            /** @var $query Builder */
            return $query
                ->where(function ($query) {
                    /** @var $query Builder */
                    $query->where('log_name', (new Task())->logName)
                        ->orWhere('log_name', (new Board())->logName)
                        ->orWhere('log_name', (new Group())->logName);
                })
                ->where('group_id', $groupId);
        })->paginate($perPage);
    }

    /**
     * @param int $perPage
     *
     * @return mixed
     */
    public function getUserActivityLog($perPage = 30)
    {
        return $this->scopeQuery(function ($query) {
            /** @var $query Builder */
            return $query
                ->select([
                    $this->activityLogTable.'.*',
                    $this->taskTable.'.id as task_id',
                    $this->taskTable.'.name as task_name',
                ])
                ->where(function ($query) {
                    /** @var $query Builder */
                    $query->where('causer_id', auth()->user()->id);
                })
                ->leftJoin($this->taskTable, function ($join) {
                    /** @var JoinClause $join */
                    $join->on($this->taskTable.'.id',$this->activityLogTable.'.task_id');
                })
                ->orderBy('created_at', 'desc');
        })
        ->paginate($perPage);
    }


    /**
     * @param string $table
     * @param int $id
     * @param array $filters
     * @param array $columns
     * @param array $range
     * @param array $users
     * @param array $assigned
     * @param array $createdBy
     * @param array $subscribed
     * @param bool|null $other
     * @param bool|null $close
     * @return Builder
     */
    public function getFilteredActivityLogs(
        string $table,
        int $id,
        array $filters,
        array $columns,
        array $range,
        array $users,
        array $assigned,
        array $createdBy,
        array $subscribed,
        bool $other = null,
        bool $close = null
    ): Builder {
        $activityLogs = $this->buildActivityLogWithRelations()
            ->addSelect([
                DB::raw("null as parent_id"),
                DB::raw("'activity_log' as source"),
            ])
            ->where($table . '.id', $id);
        
        if(in_array('assigned', $filters)) {
            $filters[] = 'assigned_and_subscribed';
        }

        if (!empty($filters) && $actions = array_intersect($filters, static::ACTIVITY_LOG_FILTERS)) {
            $activityLogs = $activityLogs->where(function ($query) use ($actions, $columns) {
                foreach ($actions as $action) {
                    if ($action != 'changed') {
                        $query->orWhere($this->activityLogTable . '.action', $action);
                    }

                    if ($action == 'changed' && $columns) {
                        $query->orWhere(function ($query) use ($columns) {
                            /** @var $query Builder */
                            foreach ($columns as $column) {
                                $query->orWhere($this->activityLogTable.'.field', $column);
                            }
                        });
                    }
                }
            });
        }

        if (count($range) === 2) {
            $activityLogs = $activityLogs->whereBetween($this->activityLogTable.'.created_at', [
                Carbon::parse($range[0]),
                Carbon::parse($range[1])
            ]);
        }

        if (count($users) > 0) {
            $activityLogs   = $activityLogs->whereIn($this->userTenantTable.'.user_id', $users);
        }

        if (count($assigned) > 0) {
            $activityLogs = $activityLogs
                ->leftJoin($this->userTenantTaskTable, function ($join) {
                    /** @var JoinClause $join */
                    $join->on($this->userTenantTaskTable.'.task_id',$this->taskTable.'.id');
                })
                ->whereIn($this->userTenantTaskTable.'.user_tenant_id', $assigned);
        }

        if (count($createdBy) > 0) {
            $activityLogs = $activityLogs
                ->whereIn($this->activityLogTable.'.causer_id', $createdBy)
                ->where('causer_type', User::class);
        }

        if (count($subscribed) > 0) {
            $activityLogs = $activityLogs
                ->join($this->notificationSubscriptionTable, function ($join) {
                    /** @var JoinClause $join */
                    $join->on($this->notificationSubscriptionTable.'.task_id',$this->taskTable.'.id');
                })
                ->whereIn($this->notificationSubscriptionTable.'.user_id', $subscribed);
        }

        /** Output actions that do not belong to the task */
        if (!is_null($other)) {
            $operator = filter_var($other, FILTER_VALIDATE_BOOLEAN) ? '=' : '!=';

            return $activityLogs
                ->where($this->activityLogTable . '.task_id', $operator, null)
                ->orderByDesc('created_at');
        }

        if (!is_null($close)) {
            $operator = $close ? '!=' : '=';
            $activityLogs = $activityLogs->where($this->taskTable . '.done_by', $operator, null);
        }

        return $activityLogs;
    }
}