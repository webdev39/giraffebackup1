<?php

namespace App\Repositories;

use App\Models\Pause;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class PauseRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @property Pause $model
 */
class PauseRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Pause::class;
    }

    /**
     * @param array $timerIds
     *
     * @return Collection
     */
    public function getPausesByTimerIds(array $timerIds) : Collection
    {
        return DB::table($this->pauseTable)
            ->select([
                $this->pauseTable.'.*',
            ])
            ->whereIn($this->pauseTable.'.timer_id', $timerIds)
            ->get();
    }

    /**
     * @param array $timerIds
     *
     * @return bool|null
     * @throws \Exception
     */
    public function deletePausesByTimerIds(array $timerIds)
    {
        return $this->model->whereIn('timer_id', $timerIds)->delete();
    }

    /**
     * @param array  $timerIds
     * @param string $endTime
     *
     * @return bool
     */
    public function stopPauseByTimerIds(array $timerIds, string $endTime)
    {
        return $this->model->whereIn('timer_id', $timerIds)->whereNull('end_time')->update([
            'end_time' => $endTime
        ]);
    }








    public function getCurrentPauseByTimerId(int $timerId)
    {
        return Pause::where([
            ['timer_id', '=', $timerId]
        ])->whereNull('end_time')->first();
    }

    public function getDetailsPauses($canReadReportsFully, $userTenant)
    {
        $pauseTable          = app('PauseRepo')->model->getTable();
        $userTenantTable      = app('UserTenantRepo')->model->getTable();
        $tenantTable          = app('TenantRepo')->model->getTable();
        $timerTable           = app('TimerRepo')->model->getTable();

        $query = DB::table($pauseTable)
            ->select(
            $timerTable . '.id',
            DB::raw("SUM(TIME_TO_SEC(TIMEDIFF(pauses.end_time,pauses.start_time))) as total_pause")
            )
            ->join(
                $timerTable,
                $timerTable . '.id',
                '=',
                $pauseTable . '.timer_id'
            )
            ->when(!$canReadReportsFully, function ($query) use ($timerTable, $userTenant){
                return $query->where($timerTable . '.user_tenant_id', '=', $userTenant->id);
            }, function ($query)use ($timerTable, $userTenantTable, $tenantTable, $userTenant){
                return $query
                    ->leftJoin(
                    $userTenantTable,
                    $userTenantTable . '.id',
                    '=',
                    $timerTable . '.user_tenant_id')
                    ->join(
                        $tenantTable,
                        function ($join) use ($userTenantTable, $tenantTable, $userTenant){
                            $join->on($tenantTable . '.id',  '=',  $userTenantTable . '.tenant_id')
                                ->where($tenantTable . '.id', '=', $userTenant->tenant_id);
                        }
                    );
            })
            ->whereNotNull('pauses.end_time')
            ->groupBy($timerTable . '.id')
            ->get();
        return $query;
    }

    public function getSummaryPauses($canReadReportsFully, $userTenant)
    {
        $pauseTable          = app('PauseRepo')->model->getTable();
        $userTenantTable      = app('UserTenantRepo')->model->getTable();
        $tenantTable          = app('TenantRepo')->model->getTable();
        $timerTable           = app('TimerRepo')->model->getTable();
        $taskTable            = app('TaskRepo')->model->getTable();

        $query = DB::table($pauseTable)
            ->select(
                $taskTable . '.id',
                $taskTable . '.name',
                DB::raw("SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(pauses.end_time,pauses.start_time)))) as total_pause")
            )
            ->join(
                $timerTable,
                $timerTable . '.id',
                '=',
                $pauseTable . '.timer_id'
            )
            ->when(!$canReadReportsFully, function ($query) use ($timerTable, $userTenant){
                return $query->where($timerTable . '.user_tenant_id', '=', $userTenant->id);
            }, function ($query)use ($timerTable, $userTenantTable, $tenantTable, $userTenant){
                return $query
                    ->leftJoin(
                        $userTenantTable,
                        $userTenantTable . '.id',
                        '=',
                        $timerTable . '.user_tenant_id')
                    ->join(
                        $tenantTable,
                        function ($join) use ($userTenantTable, $tenantTable, $userTenant){
                            $join->on($tenantTable . '.id',  '=',  $userTenantTable . '.tenant_id')
                                ->where($tenantTable . '.id', '=', $userTenant->tenant_id);
                        }
                    );
            })
            ->leftJoin(
                $taskTable,
                $taskTable . '.id',
                '=',
                $timerTable . '.task_id'
            )
            ->whereNotNull('pauses.end_time')
            ->groupBy($taskTable . '.id')
            ->get();
        return $query;
    }

}
