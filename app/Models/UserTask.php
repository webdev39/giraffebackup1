<?php

namespace App\Models;

/**
 * App\Models\UserTask
 *
 * @property int $id
 * @property int|null $user_tenant_id
 * @property int|null $task_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTask whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTask whereUserTenantId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTask query()
 */
class UserTask extends BaseModel
{
    protected $fillable = [
        'user_tenant_id',
        'task_id'
    ];

    protected $table = 'user_task';
}
