<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Log
 *
 * @property int $id
 * @property string|null $body
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attachment[] $attachments
 * @property-read mixed $can_delete
 * @property-read bool $can_update
 * @property-read mixed $log_date
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Timer[] $timer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $timer_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log query()
 */
class Log extends BaseModel
{
    /** @var  Timer */
    protected $timer;

    /** @var  Task */
    protected $task;

    /** @var  UserTenant */
    protected $userTenant;

    /** @var  UserTenantGroup */
    protected $userTenantGroup;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body',
        'created_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'canUpdate',
        'canDelete',
    ];

    /**
     * @return mixed
     */
    public function getTimerIdAttribute()
    {
        return optional($this->timer()->first())->id;
    }





    /**
     * @return bool
     */
    public function getCanUpdateAttribute()
    {
        if (!$this->id) {
            return false;
        }

        if (!$this->timer) {
            /** @var Timer timer */
            $this->timer = $this->timer()->first();
        }

        if (!$this->task) {
            if (!is_null($this->timer->task_id)){
                $this->task = app('TaskSer')->getTaskById($this->timer->task_id);
            }
        }

        if (!$this->userTenant) {
            $this->userTenant = Auth::userTenant();
        }

        if ($this->timer->user_tenant_id == $this->userTenant->id) {
            return true;
        }

        if (!$this->userTenantGroup) {
            $this->userTenantGroup = Auth::userTenantGroup($this->task->groupDetail['groupId']);
        }

        return $this->userTenant->can(Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION['name']) ||
            $this->userTenantGroup->can(Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION['name']);
    }

    public function getCanDeleteAttribute()
    {
        if (!$this->id) {
            return false;
        }

        if (!$this->timer) {
            /** @var Timer timer */
            $this->timer = $this->timer()->first();
        }

        if (!$this->task) {
            if (!is_null($this->timer->task_id)) {
                $this->task = app('TaskSer')->getTaskById($this->timer->task_id);
            }
        }

        if (!$this->userTenant) {
            $this->userTenant = Auth::userTenant();
        }

        if ($this->timer->user_tenant_id == $this->userTenant->id) {
            return true;
        }

        if (!$this->userTenantGroup) {
            $this->userTenantGroup = Auth::userTenantGroup($this->task->groupDetail['groupId']);
        }

        return $this->userTenant->can(Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION['name']) ||
            $this->userTenantGroup->can(Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION['name']);
    }

    public function timer()
    {
        return $this->belongsToMany(Timer::class, (new TimerLog())->getTable(), 'log_id', 'timer_id');
    }

    /**
     * @return mixed
     */
    public function getTaskAttribute()
    {
        return optional(optional($this->timer)->first())->task;
    }

    public function attachments()
    {
        return $this->belongsToMany(Attachment::class, 'log_attachment', 'log_id', 'attachment_id');
    }
}
