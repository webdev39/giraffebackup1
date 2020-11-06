<?php

namespace App\Models;

use App\Services\Time\TimeService;
use Carbon\Carbon;

/**
 * App\Models\Timer
 *
 * @property int $id
 * @property int $user_tenant_id
 * @property int|null $task_id
 * @property string|null $comment
 * @property string|null $start_time
 * @property string|null $end_time
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\TimerBilling $billing
 * @property-read \DateInterval $time
 * @property-read \Illuminate\Database\Eloquent\Collection $timer_pauses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log[] $log
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Pause[] $pauses
 * @property-read \App\Models\Task|null $task
 * @property-read \App\Models\UserTenant $userTenant
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Timer whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Timer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Timer whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Timer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Timer whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Timer whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Timer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Timer whereUserTenantId($value)
 * @mixin \Eloquent
 * @property-read mixed $log_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Timer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Timer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Timer query()
 */
class Timer extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'timers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment',
        'user_tenant_id',
        'task_id',
        'start_time',
        'end_time',
        'created_at'
    ];

    /**
     * @return mixed
     */
    public function getLogIdAttribute()
    {
        return optional($this->log()->first())->id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userTenant()
    {
        return $this->belongsTo(UserTenant::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }







    /**
     * Get time of timer.
     *
     * @return \DateInterval
     */
    public function getTimeAttribute()
    {
        return $this->attributes['time'] = TimeService::getSumTimeByTimer($this);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTimerPausesAttribute()
    {
        return $this->attributes['timerPauses'] = $this->pauses()->get();
    }


    //time field + timerPauses
    protected $appends = ['time', 'timerPauses'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pauses()
    {
        return $this->hasMany(Pause::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function log()
    {
        return $this->belongsToMany(Log::class, (new TimerLog())->getTable(), 'timer_id', 'log_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function billing()
    {
        return $this->hasOne(TimerBilling::class);
    }

}
