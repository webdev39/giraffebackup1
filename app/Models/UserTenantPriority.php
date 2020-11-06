<?php

namespace App\Models;

use App\Events\Eloquent\Deleted\DeletedUserTenantTaskEvent;
use App\Events\Eloquent\Saved\SavedUserTenantTaskEvent;

/**
 * App\Models\UserTenantTask
 *
 * @property int $id
 * @property int|null $user_tenant_id
 * @property int|null $task_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Task|null $task
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTask whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTask whereUserTenantId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\UserTenant|null $userTenant
 * @property int $priority_id
 * @property int $is_invisible
 * @property-read \App\Models\Priority $priority
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantPriority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantPriority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantPriority query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantPriority whereIsInvisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantPriority wherePriorityId($value)
 */
class UserTenantPriority extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_tenant_priority';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_tenant_id',
        'priority_id',
        'is_invisible',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userTenant()
    {
        return $this->belongsTo(UserTenant::class);
    }
}
