<?php

namespace App\Models;

/**
 * App\Models\PersonalDeadline
 *
 * @property int $id
 * @property int $user_tenant_id
 * @property int $task_id
 * @property string|null $planned_deadline
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonalDeadline whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonalDeadline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonalDeadline wherePlannedDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonalDeadline whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonalDeadline whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonalDeadline whereUserTenantId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonalDeadline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonalDeadline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PersonalDeadline query()
 */
class PersonalDeadline extends BaseModel
{
    protected $fillable = ['planned_deadline', 'user_tenant_id', 'task_id'];
    protected $table = 'personal_deadlines';
}
