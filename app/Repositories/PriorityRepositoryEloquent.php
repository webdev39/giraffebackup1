<?php

namespace App\Repositories;

use App\Models\Priority;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PriorityRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Priority::class;
    }

    /**
     * @param int|null $userTenantId
     *
     * @return mixed
     */
    public function buildPriorityWithRelations(int $userTenantId = null)
    {
        return DB::table($this->priorityTable)
            ->select([
                $this->priorityTable.'.id',
                $this->priorityTable.'.name',
                $this->priorityTable.'.description',
                $this->priorityTable.'.color',
                $this->priorityTable.'.sort_order',
                $this->priorityTable.'.is_default',
                $this->priorityTable.'.is_primary',
                $this->boardTable.'.id as board_id',
                $this->userTenantPriority.'.is_invisible',
            ])
            ->leftJoin($this->boardPriorityTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->boardPriorityTable.'.priority_id', $this->priorityTable.'.id');
            })
            ->leftJoin($this->boardTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->boardTable.'.id', $this->boardPriorityTable.'.board_id');
//                $join->where($this->boardTable.'.is_archive', 0);
            })
            ->leftJoin($this->groupTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->groupTable.'.id', '=', $this->boardTable.'.group_id');
            })
            ->leftJoin($this->userTenantGroupTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTenantGroupTable.'.group_id', '=', $this->groupTable.'.id');
            })
            ->leftJoin($this->userTenantTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTenantTable.'.id', '=', $this->userTenantGroupTable.'.user_tenant_id');
            })
            ->leftJoin($this->userTenantPriority, function ($join) use ($userTenantId) {
                /** @var JoinClause $join */
                $join->on($this->userTenantPriority.'.priority_id', $this->priorityTable.'.id')
                    ->where($this->userTenantPriority.'.user_tenant_id', $userTenantId);
            })
            ->orderBy($this->priorityTable.'.id')
            ->distinct();
    }

    /**
     * @param int $userTenantId
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPrioritiesByUserTenantId(int $userTenantId)
    {
        return $this->buildPriorityWithRelations($userTenantId)
            ->where($this->userTenantTable.'.id', $userTenantId)
            ->get();
    }

    /**
     * @param array $boardIds
     * @param int   $userTenantId
     *, Auth::userTenantId()
     * @return Collection
     */
    public function getPrioritiesByBoardIds(array $boardIds, int $userTenantId) : Collection
    {
        return $this->buildPriorityWithRelations($userTenantId)
            ->whereIn($this->boardTable.'.id', $boardIds)
            ->get();
    }

    /**
     * @param int $boardId
     *
     * @return \stdClass
     */
    public function getPrimaryPriorityByBoardId(int $boardId) : ?\stdClass
    {
        return $this->buildPriorityWithRelations()
            ->where($this->boardTable.'.id', $boardId)
            ->where($this->priorityTable.'.is_primary', 1)
            ->get()
            ->first();
    }

    /**
     * @param int $priority
     * @param int $userTenantId
     *
     * @return mixed
     */
    public function getPriorityWithRelationsById(int $priority, int $userTenantId)
    {
        return $this->buildPriorityWithRelations($userTenantId)
            ->where($this->priorityTable.'.id', $priority)
            ->get()
            ->first();
    }

    /**
     * @param array $boardIds
     *
     * @return int
     */
    public function removePriorityByBoardIds(array $boardIds) : int
    {
        return $this->buildPriorityWithRelations()
            ->where($this->boardTable.'.id', null)
            ->where($this->priorityTable.'.is_default', 0)
            ->delete();
    }

    /**
     * @param array $orders
     *
     * @return mixed
     * @throws \Throwable
     */
    public function updateTaskOrders(array $orders)
    {
        return DB::transaction(function () use ($orders) {
            foreach ($orders as $order => $priorityId) {
                $this->update(['sort_order' => $order + 1], $priorityId);
            }
        });
    }
}
