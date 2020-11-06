<?php

namespace App\Services\Budget;

use App\Services\BaseRequest;

class BudgetRequest extends BaseRequest
{
    public $softBudget;
    public $hardBudget;
    public $budgetTypeId;

    /**
     * BudgetRequest constructor.
     *
     * @param string $softBudget
     * @param string $hardBudget
     * @param int    $budgetTypeId
     */
    public function __construct(string $softBudget = '00:00', string $hardBudget = '00:00', int $budgetTypeId = 1)
    {
        $this->softBudget   = $softBudget;
        $this->hardBudget   = $hardBudget;
        $this->budgetTypeId = $budgetTypeId;
    }
}