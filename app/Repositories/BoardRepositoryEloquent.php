<?php

namespace App\Repositories;

use App\Models\Board;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class BoardRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @property Board $model
 */
class BoardRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Board::class;
    }

    /**
     * @return \Illuminate\Database\Query\Builder|static
     */
    private function buildGroupWithRelations()
    {
        $quickNavSubQuery = Auth::id()
            ? DB::raw('(SELECT quick_nav FROM ' . $this->userBoardSettingsTable.' WHERE board_id = ' . $this->boardTable . '.id AND user_id = ' . Auth::id() .') AS quick_nav' )
            : DB::raw('null AS quick_nav');
        return DB::table($this->boardTable)
            ->select([
                $this->boardTable.'.id',
                $this->boardTable.'.name',
                $this->boardTable.'.description',
                $quickNavSubQuery,
                $this->boardTable.'.deadline',
                $this->boardTable.'.is_archive',
                $this->boardTable.'.updated_at',
                $this->boardTable.'.created_at',
                $this->boardTable.'.creator_id',
                $this->boardTable.'.budget_id',
                $this->boardTable.'.priority_id',
                $this->boardTable.'.view_type_id',
                $this->boardTable.'.hide_done_tasks',
                $this->groupTable.'.id              as group_id',
                $this->groupTable.'.name            as group_name',
                $this->budgetTable.'.id             as budget_id',
                $this->budgetTable.'.soft_budget    as soft_budget',
                $this->budgetTable.'.hard_budget    as hard_budget',
                $this->budgetTable.'.budget_type_id as budget_type_id',
            ])
            ->leftJoin($this->budgetTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->budgetTable.'.id', '=', $this->boardTable.'.budget_id');
            })
            ->join($this->groupTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->groupTable.'.id', $this->boardTable.'.group_id');
            });
    }

    /**
     * @param array $boardsIds
     *
     * @return Collection
     */
    public function getBoardsByIds(array $boardsIds) : Collection
    {
        return $this->buildGroupWithRelations()
            ->whereIn($this->boardTable.'.id', $boardsIds)
            ->get();
    }

    /**
     * @param array $groupIds
     *
     * @return Collection
     */
    public function getBoardsByGroupIds(array $groupIds) : Collection
    {
        return $this->buildGroupWithRelations()
            ->whereIn($this->groupTable.'.id', $groupIds)
            ->get();
    }

    /**
     * @param int $userTenantId
     *
     * @return Collection
     */
    public function getBoardsByUserTenantId(int $userTenantId) : Collection
    {
        return $this->buildGroupWithRelations()
            ->join($this->userTenantGroupTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTenantGroupTable.'.group_id', $this->boardTable.'.group_id');
            })
            ->where($this->userTenantGroupTable.'.user_tenant_id', $userTenantId)
            ->get();
    }

    /**
     * @param int $tenantId
     *
     * @return Collection
     */
    public function getBoardsWithRelationsByTenantId(int $tenantId) : Collection
    {
        return $this->buildGroupWithRelations()
            ->join($this->userTenantGroupTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTenantGroupTable.'.group_id', $this->boardTable.'.group_id');
            })
            ->join($this->userTenantTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTenantTable.'.id', '=', $this->userTenantGroupTable.'.user_tenant_id');
            })
            ->where($this->userTenantTable.'.tenant_id', $tenantId)
            ->distinct()
            ->get();
    }

    /**
     * @param int $userTenantId
     * @param int $lastCount
     *
     * @return Collection
     */
    public function getLatestActiveBoardsByUserTenantId(int $userTenantId, int $lastCount = 5)
    {
        return $this->buildGroupWithRelations()
            ->addSelect([
                DB::raw("$this->timerTable.updated_at as timer_updated_at")
            ])
            ->join($this->timerTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->timerTable.'.updated_at',
                    DB::raw("(
                        SELECT max($this->timerTable.updated_at)
                        FROM $this->timerTable
                        JOIN $this->boardTaskTable ON $this->boardTaskTable.task_id = $this->timerTable.task_id
                        WHERE $this->boardTaskTable.board_id = $this->boardTable.id
                    )")
                );
            })
            ->where($this->timerTable.'.user_tenant_id', $userTenantId)
            ->where($this->boardTable.'.is_archive',0)
            ->latest('timer_updated_at')
            ->limit($lastCount)
            ->get();
    }

    /**
     * @param array $boardIds
     * @param bool  $isArchive
     *
     * @throws \Throwable
     */
    public function changeIsArchiveBoardByIds(array $boardIds, bool $isArchive)
    {
        DB::transaction(function () use ($boardIds, $isArchive) {
            $boards = $this->model->findMany($boardIds);

            $boards->each(function ($board) use ($isArchive) {
                /** @var Board $board */
                $board->is_archive = $isArchive ? 1 : 0;
                $board->save();
            });
        });
    }
}
