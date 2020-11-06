<?php

namespace App\Repositories;

use App\Models\Timer;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class TimerRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @property Timer $model
 */
class TimerRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Timer::class;
    }

    /**
     * @return Builder|static
     */
    private function buildTimerWithRelations()
    {
        return DB::table($this->timerTable)
            ->select([
                $this->timerTable.'.*',
                $this->taskTable.'.id                           as task_id',
                $this->taskTable.'.name                         as task_name',
                $this->taskTable.'.done_by                      as task_done_by',
                $this->boardTable.'.id                          as board_id',
                $this->boardTable.'.name                        as board_name',
                $this->groupTable.'.id                          as group_id',
                $this->groupTable.'.name                        as group_name',
                $this->userTenantTable.'.user_id                as user_id',
                $this->timerBillingTable.'.billing_status_id    as billing_status_id',
                $this->timerBillingTable.'.id                   as timer_billing_id'
            ])
            ->leftJoin($this->timerBillingTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->timerBillingTable.'.timer_id', '=', $this->timerTable.'.id');
            })
            ->leftJoin($this->userTenantTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->timerTable.'.user_tenant_id', '=', $this->userTenantTable.'.id');
            })
            ->leftJoin($this->taskTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->taskTable.'.id', '=', $this->timerTable.'.task_id');
            })
            ->leftJoin($this->boardTaskTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->taskTable.'.id', '=', $this->boardTaskTable.'.task_id');
            })
            ->leftJoin($this->boardTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->boardTable.'.id', '=', $this->boardTaskTable.'.board_id');
            })
            ->leftJoin($this->groupTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->groupTable.'.id', '=', $this->boardTable.'.group_id');
            });
    }

    /**
     * @param array $ids
     *
     * @return Collection
     */
    public function getTimersByIds(array $ids)
    {
        return $this->buildTimerWithRelations()
            ->whereIn($this->timerTable.'.id', $ids)
            ->get();
    }

    /**
     * @param array $logIds
     *
     * @return Collection
     */
    public function getTimersByLogIds(array $logIds) : Collection
    {
        return $this->buildTimerWithRelations()
            ->addSelect($this->timerLogTable.'.log_id as log_id')
            ->join($this->timerLogTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->timerTable.'.id', '=', $this->timerLogTable.'.timer_id');
            })
            ->whereIn($this->timerLogTable.'.log_id', $logIds)
            ->get();
    }

    /**
     * @param array $taskIds
     *
     * @return Collection
     */
    public function getTimersByTaskIds(array $taskIds) : Collection
    {
        return $this->buildTimerWithRelations()
            ->whereIn($this->taskTable.'.id', $taskIds)
            ->get();
    }

    /**
     * @param int  $userTenantId
     * @param bool $log
     *
     * @return Collection
     */
    public function getTimersByUserTenantId(int $userTenantId, $log = false) : Collection
    {
        $query = $this->buildTimerWithRelations()
            ->where($this->timerTable.'.user_tenant_id', '=', $userTenantId)
            ->latest($this->timerTable.'.updated_at');

        if (!$log) {
            $query->where($this->timerTable.'.end_time', null);
        }

        return $query->get();
    }

    /**
     * @param int $timerId
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function getTimerByIdWithRelations(int $timerId)
    {
        return $this->buildTimerWithRelations()
            ->where($this->timerTable.'.id', $timerId)
            ->first();
    }

    /**
     * @param int $userTenantId
     * @param int $taskId
     *
     * @return Collection
     */
    public function getTimerOfCurrentTaskByUserTenantIdTaskId(int $userTenantId, int $taskId)
    {
        return $this->buildTimerWithRelations()
            ->where($this->userTenantTable.'.id', '=', $userTenantId)
            ->where($this->timerTable.'.task_id', '=', $taskId)
            ->whereNotNull($this->timerTable.'.start_time')
            ->whereNull($this->timerTable.'.end_time')
            ->get();
    }

    /**
     * @param int $userTenantId
     *
     * @return Collection
     */
    public function getActiveOrPauseTimersByUserTenantId(int $userTenantId)
    {
        return $this->buildTimerWithRelations()
            ->where($this->timerTable.'.user_tenant_id', '=', $userTenantId)
            ->where($this->timerTable.'.user_tenant_id', '=', $userTenantId)
            ->whereNotNull($this->timerTable.'.start_time')
            ->whereNull($this->timerTable.'.end_time')
            ->get();
    }

    /**
     * @param array  $timerIds
     * @param string $endTime
     *
     * @return bool
     */
    public function stopTimerByIds(array $timerIds, string $endTime)
    {
        return $this->model->whereIn('id', $timerIds)->whereNull('end_time')->update([
            'end_time' => $endTime
        ]);
    }

    /**
     * @param int $groupId
     *
     * @return int
     */
    public function getCountTimersInGroup(int $groupId) : int
    {
        return DB::table($this->timerTable)
            ->join($this->boardTaskTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->boardTaskTable.'.task_id', $this->timerTable.'.task_id');
            })
            ->join($this->boardTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->boardTable.'.id', $this->boardTaskTable.'.board_id');
            })
            ->where($this->boardTable.'.group_id', $groupId)
            ->count([
                $this->timerTable.'.id',
            ]);
    }

    /**
     * @param bool $canReadReportsFully
     * @param int $tenant_id
     * @param int $user_tenant_id
     * @param bool $selectGroups
     * @param bool $selectBoards
     * @param bool $selectMembers
     * @param bool $selectClients
     * @return array|null
     */
    public function getReportsTimers(bool $canReadReportsFully, int $user_tenant_id, int $tenant_id,
                                     $selectGroups = false,
                                     $selectBoards = false, $selectMembers = false, $selectClients = false): ?array
    {
        $userTable            = app('UserRepo')->model->getTable();
        $userTenantTable      = app('UserTenantRepo')->model->getTable();
        $tenantTable          = app('TenantRepo')->model->getTable();
        $timerTable           = app('TimerRepo')->model->getTable();
        $taskTable            = app('TaskRepo')->model->getTable();
        $boardTaskTable       = app('BoardTaskRepo')->model->getTable();
        $boardTable           = app('BoardRepo')->model->getTable();
        $groupTable           = app('GroupRepo')->model->getTable();
        $timerBillingTable    = app('TimerBillingRepo')->model->getTable();
        $billTimersTable      = app('BillTimerRepo')->model->getTable();
        $billingStatusTable   = app('BillingStatusRepo')->model->getTable();
        $logTable             = app('LogRepo')->model->getTable();
        $timerLogTable        = app('TimerLogRepo')->model->getTable();
        $bills                = app('BillRepo')->model->getTable();

        return DB::table($timerTable)
            ->select(
                $userTenantTable . '.id as user_tenant_id',
                $userTable . '.id as user_id',
                $userTable . '.name as user_name',
                $userTable . '.last_name as user_last_name',
                $timerTable . '.id as timer_id',
                $timerTable . '.comment',
                $timerTable . '.start_time',
                $timerTable . '.end_time',
                $timerTable . '.created_at',
                $taskTable . '.id as task_id',
                $taskTable . '.name as task_name',
                $boardTable . '.id as board_id',
                $boardTable . '.name as board_name',
                $bills. '.invoice_number as bills_invoice_number',
                $groupTable . '.id as group_id',
                $groupTable . '.name as group_name',
                $timerBillingTable . '.id as timer_billing_id',
                $timerBillingTable . '.rate',
                $timerBillingTable . '.time_bill',
                $billTimersTable . '.id as bill_timer_id',
                $billTimersTable . '.bill_id as bill_id',
                $billTimersTable . '.title as bill_timer_title',
                $billTimersTable . '.time as bill_timer',
                $billTimersTable . '.deleted_at as bill_timer_deleted',
                $billTimersTable . '.comment as bill_timer_comment',
                $billingStatusTable . '.id as billing_status_id',
                $billingStatusTable . '.name as billing_status_name',
                $billingStatusTable . '.alias as billing_status_alias',
                $billingStatusTable . '.color as billing_status_color',
                $logTable . '.id as log_id',
                $logTable . '.body as log_body'
            )
            ->join(
                $userTenantTable,
                $userTenantTable . '.id',
                '=',
                $timerTable . '.user_tenant_id'
            )
            ->when($canReadReportsFully, function ($query) use($tenantTable, $userTenantTable, $tenant_id) {
                return $query->join($tenantTable, function ($join) use ($tenantTable, $userTenantTable, $tenant_id) {
                    $join->on($tenantTable . '.id', '=', $userTenantTable . '.tenant_id')
                        ->where($tenantTable . '.id', '=', $tenant_id);
                });
            })
            ->leftJoin(
                $userTable,
                $userTable . '.id',
                '=',
                $userTenantTable . '.user_id'
            )
            ->leftJoin(
                $taskTable,
                $taskTable . '.id',
                '=',
                $timerTable . '.task_id'
            )
            ->leftJoin(
                $boardTaskTable,
                $taskTable . '.id',
                '=',
                $boardTaskTable . '.task_id'
            )
            ->leftJoin(
                $boardTable,
                $boardTable . '.id',
                '=',
                $boardTaskTable . '.board_id'
            )
            ->leftJoin(
                $groupTable,
                $groupTable . '.id',
                '=',
                $boardTable . '.group_id'
            )
            ->leftJoin(
                $timerBillingTable,
                $timerBillingTable . '.timer_id',
                '=',
                $timerTable . '.id'
            )
            ->leftJoin(
                $billTimersTable,
                $billTimersTable . '.timer_billing_id',
                '=',
                $timerBillingTable . '.id'
            )
            ->leftJoin(
                $bills,
                $bills . '.id',
                '=',
                $billTimersTable . '.bill_id'
            )
            ->leftJoin(
                $billingStatusTable,
                $billingStatusTable . '.id',
                '=',
                $timerBillingTable . '.billing_status_id'
            )
            ->leftJoin(
                $timerLogTable,
                $timerLogTable . '.timer_id',
                '=',
                $timerTable . '.id'
            )
            ->leftJoin(
                $logTable,
                $logTable . '.id',
                '=',
                $timerLogTable . '.log_id'
                )
            ->when(!$canReadReportsFully, function ($query) use ($timerTable, $user_tenant_id){
                return $query->where($timerTable . '.user_tenant_id', '=', $user_tenant_id);
            })
            ->when($selectGroups, function ($query) use ($groupTable, $selectGroups) {
                return $query->whereIn($groupTable . '.id', $selectGroups);
            })
            ->when($selectBoards, function ($query) use ($boardTable, $selectBoards) {
                return $query->whereIn($boardTable . '.id', $selectBoards);
            })
            ->when($selectMembers, function ($query) use ($userTenantTable, $selectMembers) {
                return $query->whereIn($userTenantTable . '.id', $selectMembers);
            })
            ->whereNotNull($taskTable . '.id')
            ->whereNotNull($timerTable . '.end_time')
            ->get()
            ->toArray();
    }

    /**
     * @param $canReadReportsFully
     * @param $id
     * @param $selectGroups
     * @param $selectBoards
     * @param $selectMembers
     * @param $selectClients
     * @return mixed
     */
    public function getReportsTimersSummary($canReadReportsFully, $id, $selectGroups, $selectBoards, $selectMembers, $selectClients)
    {
        $userTable            = app('UserRepo')->model->getTable();
        $userTenantTable      = app('UserTenantRepo')->model->getTable();
        $tenantTable          = app('TenantRepo')->model->getTable();
        $timerTable           = app('TimerRepo')->model->getTable();
        $taskTable            = app('TaskRepo')->model->getTable();
        $boardTaskTable       = app('BoardTaskRepo')->model->getTable();
        $boardTable           = app('BoardRepo')->model->getTable();
        $groupTable           = app('GroupRepo')->model->getTable();
        $timerBillingTable    = app('TimerBillingRepo')->model->getTable();
        $billingStatusTable   = app('BillingStatusRepo')->model->getTable();

        return DB::table($timerTable)
            ->select(
                $taskTable . '.id as task_id',
                $taskTable . '.name as task_name',
                $taskTable . '.created_at',
                $boardTable . '.id as board_id',
                $boardTable . '.name as board_name',
                $groupTable . '.id as group_id',
                $groupTable . '.name as group_name',
                DB::raw("SEC_TO_TIME(SUM(TIME_TO_SEC(timer_billings.time_bill))) as total_billed_time"),
                DB::raw("SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(timers.end_time,timers.start_time)))) as total_time_used"),
                DB::raw("GROUP_CONCAT(DISTINCT CONCAT(users.name,' ', users.last_name)) as users_name"),
                DB::raw("GROUP_CONCAT(DISTINCT billing_statuses.name) as billing_status_name"),
                DB::raw("GROUP_CONCAT(DISTINCT billing_statuses.alias) as billing_status_alias"),
                DB::raw("GROUP_CONCAT(DISTINCT billing_statuses.color) as billing_status_color")
            )
            ->join(
                $userTenantTable,
                $userTenantTable . '.id',
                '=',
                $timerTable . '.user_tenant_id'
            )
            ->when($canReadReportsFully, function ($query) use($tenantTable, $userTenantTable, $id){
                return $query->join($tenantTable, function ($join) use ($tenantTable, $userTenantTable, $id){
                    $join->on($tenantTable . '.id', '=', $userTenantTable . '.tenant_id')
                        ->where($tenantTable . '.id', '=', $id);
                });
            })
            ->leftJoin(
                $userTable,
                $userTable . '.id',
                '=',
                $userTenantTable . '.user_id'
            )
            ->leftJoin(
                $taskTable,
                $taskTable . '.id',
                '=',
                $timerTable . '.task_id'
            )
            ->leftJoin(
                $boardTaskTable,
                $taskTable . '.id',
                '=',
                $boardTaskTable . '.task_id'
            )
            ->leftJoin(
                $boardTable,
                $boardTable . '.id',
                '=',
                $boardTaskTable . '.board_id'
            )
            ->leftJoin(
                $groupTable,
                $groupTable . '.id',
                '=',
                $boardTable . '.group_id'
            )
            ->leftJoin(
                $timerBillingTable,
                $timerBillingTable . '.timer_id',
                '=',
                $timerTable . '.id'
            )
            ->leftJoin(
                $billingStatusTable,
                $billingStatusTable . '.id',
                '=',
                $timerBillingTable . '.billing_status_id'
            )
            ->when(!$canReadReportsFully, function ($query) use ($timerTable, $id){
                return $query->where($timerTable . '.user_tenant_id', '=', $id);
            })
            ->when($selectGroups, function ($query) use ($groupTable, $selectGroups) {
                return $query->whereIn($groupTable . '.id', $selectGroups);
            })
            ->when($selectBoards, function ($query) use ($boardTable, $selectBoards) {
                return $query->whereIn($boardTable . '.id', $selectBoards);
            })
            ->when($selectMembers, function ($query) use ($userTenantTable, $selectMembers) {
                return $query->whereIn($userTenantTable . '.id', $selectMembers);
            })
            ->whereNotNull($taskTable . '.id')
            ->whereNotNull($timerTable . '.end_time')
            ->groupBy('task_id','task_name','board_id','board_name','group_id', 'group_name')
            ->get();
    }

    /**
     * @param string $year
     * @return Collection
     */
    public function getBillingYearSummary(string $year)
    {
        $userTenantTable      = app('UserTenantRepo')->model->getTable();
        $timerTable           = app('TimerRepo')->model->getTable();
        $taskTable            = app('TaskRepo')->model->getTable();
        $boardTaskTable       = app('BoardTaskRepo')->model->getTable();
        $boardTable           = app('BoardRepo')->model->getTable();
        $groupTable           = app('GroupRepo')->model->getTable();
        $timerBillingTable    = app('TimerBillingRepo')->model->getTable();

        return DB::table($timerTable)
            ->select(
                $boardTable . '.id as board_id',
                $boardTable . '.name as board_name',
                $groupTable . '.id as group_id',
                $groupTable . '.name as group_name',
                DB::raw("SEC_TO_TIME(SUM(TIME_TO_SEC(timer_billings.time_bill))) as total_billed_time"),
                DB::raw("SEC_TO_TIME(SUM(TIME_TO_SEC(timers.end_time) -  TIME_TO_SEC(timers.start_time))) as total_time_used")
            )
            ->join(
                $userTenantTable,
                $userTenantTable . '.id',
                '=',
                $timerTable . '.user_tenant_id'
            )
            ->leftJoin(
                $taskTable,
                $taskTable . '.id',
                '=',
                $timerTable . '.task_id'
            )
            ->leftJoin(
                $boardTaskTable,
                $taskTable . '.id',
                '=',
                $boardTaskTable . '.task_id'
            )
            ->leftJoin(
                $boardTable,
                $boardTable . '.id',
                '=',
                $boardTaskTable . '.board_id'
            )
            ->leftJoin(
                $groupTable,
                $groupTable . '.id',
                '=',
                $boardTable . '.group_id'
            )
            ->leftJoin(
                $timerBillingTable,
                $timerBillingTable . '.timer_id',
                '=',
                $timerTable . '.id'
            )
            ->whereNotNull($taskTable . '.id')
            ->whereNotNull($timerTable . '.end_time')
            ->whereYear($boardTable . '.created_at', '=', $year)
            ->groupBy('board_id','board_name','group_id', 'group_name')
            ->get();
    }

    public function getTimersOfUserTenantByTaskId(int $userTenantId, int $taskId)
    {
        return $this->model->where([
            ['task_id', '=', $taskId],
            ['user_tenant_id', '=', $userTenantId]
        ])->whereNotNull('end_time')->get();
    }
}
