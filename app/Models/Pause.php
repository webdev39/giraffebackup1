<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Pause
 *
 * @property int $id
 * @property int $timer_id
 * @property string|null $start_time
 * @property string|null $end_time
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Timer $timer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pause whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pause whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pause whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pause whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pause whereTimerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pause whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pause newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pause newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pause query()
 */
class Pause extends BaseModel
{
    //
    protected $fillable = ['timer_id', 'start_time', 'end_time'];
    protected $table = 'pauses';

    public function timer()
    {
        return $this->belongsTo(Timer::class);
    }
}
