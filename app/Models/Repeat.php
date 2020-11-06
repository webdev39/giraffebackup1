<?php

namespace App\Models;

use Carbon\Carbon;

/**
 * App\Models\Repeat
 *
 * @property int $id
 * @property int $task_id
 * @property string|null $time_unit
 * @property int|null $time_interval
 * @property string|null $started_at
 * @property string|null $repeated_at
 * @property string|null $ended_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Task $task
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat whereEndingAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat whereStartingAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat whereTimeInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat whereTimeUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\UserTenant $userTenant
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat whereRepeatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Repeat query()
 */
class Repeat extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'repeats';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'time_unit',
        'time_interval',
        'started_at',
        'repeated_at',
        'user_tenant_id',
        'ended_at'
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
}
