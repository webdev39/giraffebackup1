<?php

namespace App\Models;

use App\Events\Eloquent\Saved\SavedBudgetEvent;
use App\Notifications\ChangeHardBudgetNotification;
use App\Notifications\ChangeSoftBudgetNotification;

/**
 * App\Models\Budget
 *
 * @property int $id
 * @property string|null $soft_budget
 * @property string|null $hard_budget
 * @property int|null $budget_type_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Budget whereBudgetTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Budget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Budget whereHardBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Budget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Budget whereSoftBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Budget whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Task $task
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Budget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Budget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Budget query()
 */
class Budget extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'budgets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'soft_budget',
        'hard_budget',
        'budget_type_id'
    ];

    /**
     * Default log name for this model
     *
     * @var string
     */
    public $logName = 'budget';

    /**
     * Logging only the changed attributes
     *
     * @var array
     */
    public $logAttributes = [
        'soft_budget',
        'hard_budget',
    ];

    /**
     * List of attributes when changing which notification of subscribers
     *
     * @var array
     */
    public $notifyAttributes = [
        'soft_budget'   => ChangeSoftBudgetNotification::class,
        'hard_budget'   => ChangeHardBudgetNotification::class,
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => SavedBudgetEvent::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function task()
    {
        return $this->hasMany(Task::class);
    }
}
