<?php

namespace App\Models;

/**
 * App\Models\BudgetType
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BudgetType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BudgetType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BudgetType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BudgetType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BudgetType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BudgetType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BudgetType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BudgetType query()
 */
class BudgetType extends BaseModel
{
    protected $fillable = ['name', 'description'];
    protected $table = 'budget_types';
}
