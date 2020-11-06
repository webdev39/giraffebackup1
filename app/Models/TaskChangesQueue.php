<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TaskChangesQueue
 * @package App\Models
 * @property string $new_value
 * @property string $old_value
 * @property array $subscribers
 * @property int $task_id
 * @property string $field
 * @property string $notification_class
 * @property-read Task $task
 * @property-read User $sender
 */
class TaskChangesQueue extends Model
{
    protected $table = 'task_changes_queue';

    protected $fillable = ['sender_id', 'task_id', 'field', 'notification_class', 'subscribers', 'new_value', 'old_value'];

    protected $casts = [
        'subscribers' => 'array'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
