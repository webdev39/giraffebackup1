<?php

namespace App\Models;

/**
 * App\Models\TimerLog
 *
 * @property int $id
 * @property int $timer_id
 * @property int $log_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerLog whereLogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerLog whereTimerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerLog whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerLog query()
 */
class TimerLog extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'timer_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'timer_id',
        'log_id'
    ];
}
