<?php

namespace App\Repositories;

use App\Models\Filter;
use App\Models\TaskSortOrder;
use App\Services\Time\TimeService;
use App\Services\Timer\TimerService;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class FilterRepositoryEloquent
 * @package App\Repositories
 */
class FilterRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Filter::class;
    }

    /**
     * @param int $filterId
     *
     * @return Collection
     */
    public function findTaskIdsByFilterId(int $filterId)
    {
        $filter = $this->find($filterId);

        $query = DB::table($this->filterTable)
            ->select([
                $this->taskTable.'.id                   as task_id',
                $this->userTaskSortOrderTable.'.order   as sort_order',
            ])
            ->join($this->userTenantTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTenantTable.'.id', $this->filterTable.'.user_tenant_id');
            })
            ->join($this->userTenantGroupTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTenantGroupTable.'.user_tenant_id', $this->userTenantTable.'.id');
            })
            ->join($this->groupTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->groupTable.'.id', $this->userTenantGroupTable.'.group_id');
            })
            ->join($this->boardTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->boardTable.'.group_id', $this->groupTable.'.id');
            })
            ->join($this->boardTaskTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->boardTaskTable.'.board_id', $this->boardTable.'.id');
            })
            ->join($this->taskTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->taskTable.'.id', $this->boardTaskTable.'.task_id');
            })
            ->leftJoin($this->personalDeadlineTable, function ($join) use ($filter) {
                /** @var JoinClause $join */
                $join->on($this->taskTable.'.id',  $this->personalDeadlineTable.'.task_id')
                    ->where($this->personalDeadlineTable.'.user_tenant_id', $filter->user_tenant_id);
            })
            ->leftJoin($this->userTaskSortOrderTable, function ($join) use ($filter) {
                /** @var JoinClause $join */
                $join->on($this->userTaskSortOrderTable.'.task_id',  $this->taskTable.'.id')
                    ->where($this->userTaskSortOrderTable.'.model_id',  $filter->id)
                    ->where($this->userTaskSortOrderTable.'.user_id',  $filter->user_tenant_id)
                    ->where($this->userTaskSortOrderTable.'.type',  TaskSortOrder::SORT_ORDER_TYPES['filter']['name']);
            })
            ->where($this->filterTable.'.id', $filter->id);

        // filter by group ids
        if ($filter->group_ids) {
            $query = $query->whereIn($this->groupTable.'.id', $filter->group_ids);
        }

        // filter by board ids
        if ($filter->board_ids) {
            $query = $query->whereIn($this->boardTable.'.id', $filter->board_ids);
        }

        // filter by user tenants assigned to the task
        if ($filter->assigner_ids) {
            $query = $query
                ->join($this->userTenantTaskTable, function ($join) {
                    /** @var JoinClause $join */
                    $join->on($this->userTenantTaskTable.'.task_id', $this->taskTable.'.id');
                })
                ->whereIn($this->userTenantTaskTable.'.user_tenant_id', $filter->assigner_ids);
        }

        // filter by priority ids
        if ($filter->priority_ids) {
            $query = $query
                ->join($this->priorityTable, function ($join) {
                    /** @var JoinClause $join */
                    $join->on($this->priorityTable.'.id', $this->taskTable.'.priority_id');
                })
                ->whereIn($this->priorityTable.'.id', $filter->priority_ids);
        }

        // filter by status
        if (!is_null($filter->status)) {
            if ($filter->status) {
                $query = $query->where($this->taskTable.'.done_by', '!=', null);
            } else {
                $query = $query->where($this->taskTable.'.done_by', '=', null);
            }
        }

        if ($filter->range) {
            $range = $this->filterRangeToCarbon($filter->range);

            $query->where($this->personalDeadlineTable.'.planned_deadline', '>=', $range['start']);
            $query->where($this->personalDeadlineTable.'.planned_deadline', '<=', $range['end']);
        }

        return $query->get();
    }

    /**
     * @param $range
     *
     * @return array
     */
    public function filterRangeToCarbon($range)
    {
        $startRange = Carbon::now();
        $endRange   = Carbon::now();

        switch ($range) {
            case Filter::TODAY:
                $startRange->startOfDay();
                $endRange->endOfDay();
                break;
            case Filter::YESTERDAY:
                $startRange->subDay()->startOfDay();
                $endRange->subDay()->endOfDay();
                break;
            case Filter::TOMORROW:
                $startRange->addDay()->startOfDay();
                $endRange->addDay()->endOfDay();
                break;
            case Filter::NEXT_WEEK:
                $startRange->endOfWeek()->endOfDay()->addSecond(1);
                $endRange->endOfWeek()->addWeek()->endOfDay();
                break;
            case Filter::LAST_WEEK:
                $startRange->startOfWeek()->subWeek()->startOfDay();
                $endRange->startOfWeek()->subSecond(1);
                break;
            case Filter::THIS_WEEK:
                $startRange->startOfWeek()->startOfDay();
                $endRange->endOfWeek()->endOfDay();
                break;
            case Filter::THIS_MONTH:
                $startRange->startOfMonth()->startOfDay();
                $endRange->endOfMonth()->endOfDay();
                break;
            case Filter::LAST_MONTH:
                $startRange->startOfMonth()->subMonth()->startOfDay();
                $endRange->startOfMonth()->subSecond(1);
                break;
            default:
                $range      = explode('/', $range);

                $startRange = $range[0] ?? null;
                $endRange   = $range[1] ?? $range[0];

                $startRange = Carbon::createFromTimestamp($startRange);
                $endRange   = Carbon::createFromTimestamp($endRange)->endOfDay();
        }


        return [
            'start' => TimeService::toUserLocalTime($startRange),
            'end'   => TimeService::toUserLocalTime($endRange),
        ];
    }
}
