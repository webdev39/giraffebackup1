<?php

namespace App\Events\Eloquent\Saved;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Models\Budget;

/**
 * Class SavedBudgetEvent
 *
 * @package App\Events\Eloquent
 */
class SavedBudgetEvent extends BaseEloquentEvent
{
    /** @var Budget */
    public $model;
}