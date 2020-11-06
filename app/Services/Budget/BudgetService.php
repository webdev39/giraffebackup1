<?php

namespace App\Services\Budget;

use App\Repositories\BudgetRepositoryEloquent;
use App\Services\BaseService;
use App\Services\Time\TimeService;
use Carbon\CarbonInterval;
use Illuminate\Support\Collection;

class BudgetService extends BaseService
{
    /**
     * @var BudgetRepositoryEloquent
     */
    private $budgetRepo;

    /**
     * BudgetService constructor.
     */
    public function __construct()
    {
        $this->budgetRepo = app('BudgetRepo');
    }

    /**
     * @param Collection $tasks
     *
     * @return int
     * @throws \Exception
     */
    public static function calcBoardBudget(Collection $tasks) : int
    {
        $budget = CarbonInterval::create(0);

        foreach ($tasks as $task) {
            if ($taskBudget = $task->hard_budget ?? "00:00") {
                list($hours, $minutes) = explode(":", $taskBudget);

                $budget->hours += intval($hours);
                $budget->minutes += intval($minutes);
            }
        }

        return TimeService::getTotalMinutes($budget);
    }

    /**
     * @param string $softBudget
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createDefaultBudget($softBudget = '00:00')
    {
        return $this->createOrUpdateBudget([
            'soft_budget'       => $softBudget,
            'hard_budget'       => '00:00',
            'budget_type_id'    => app('FieldRepo')->getDefaultBudgetType()->id,
        ]);
    }

    /**
     * @param      $attributes
     * @param null $budgetId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createOrUpdateBudget($attributes, $budgetId = null)
    {
        if ($budgetId) {
            return $this->budgetRepo->update($attributes, $budgetId);
        }

        return $this->budgetRepo->create($attributes);
    }
}
