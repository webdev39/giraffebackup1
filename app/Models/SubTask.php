<?php

namespace App\Models;

use App\Events\Eloquent\Saved\SavedSubTaskEvent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SubTask
 *
 * @property int $id
 * @property int|null $task_id
 * @property string $name
 * @property int $is_completed
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubTask whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubTask whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubTask whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubTask whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubTask whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubTask query()
 */
class SubTask extends BaseModel
{
    protected $fillable = ['name', 'is_completed', 'task_id', 'creator_id', 'completed_by_id'];
    protected $table = 'sub_tasks';

    /**
     * Default log name for this model
     *
     * @var string
     */
    public $logName = 'subTask';

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => SavedSubTaskEvent::class,
    ];

    /**
     * @return BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * @return BelongsTo
     */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by_id');
    }
}
