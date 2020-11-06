<?php

namespace App\Repositories;

use App\Models\Budget;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

/**
 * Class BudgetRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @property Budget $model
 */
class BudgetRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Budget::class;
    }

    /**
     * @param array $budgetIds
     *
     * @return bool|null
     * @throws \Exception
     */
    public function removeBudgetByIds(array $budgetIds)
    {
        return $this->model->withoutGlobalScopes()->whereIn('id', $budgetIds)->delete();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getUnusedBudgets()
    {
        return DB::table($this->budgetTable)
            ->select([
                $this->budgetTable.'.id',
                $this->budgetTable.'.soft_budget',
                $this->budgetTable.'.hard_budget',
                $this->budgetTable.'.budget_type_id',
            ])
            ->leftJoin($this->taskTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->taskTable.'.budget_id', $this->budgetTable.'.id');
            })
            ->leftJoin($this->boardTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->boardTable.'.budget_id', $this->budgetTable.'.id');
            })
            ->whereNull($this->taskTable.'.id')
            ->whereNull($this->boardTable.'.id')
            ->get();
    }
}
