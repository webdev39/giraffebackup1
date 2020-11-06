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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTask query()
 */
class UserTenantTask extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_tenant_task';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_tenant_id',
        'task_id'
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved'     => SavedUserTenantTaskEvent::class,
        'deleting'  => DeletedUserTenantTaskEvent::class
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userTenant()
    {
        return $this->belongsTo(UserTenant::class);
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->userTenant->user;
    }
}