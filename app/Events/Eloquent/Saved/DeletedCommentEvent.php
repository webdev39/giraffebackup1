<?php

namespace App\Events\Eloquent\Saved;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

/**
 * Class SavedCommentEvent
 *
 * @package App\Events\Eloquent
 */
class DeletedCommentEvent extends BaseEloquentEvent implements ShouldBroadcastNow
{
    /** @var Comment */
    public $model;

    /**
     * Get the channels the event should broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn(): Channel
    {
        $groupId = $this->model->task ? $this->model->task->board->first()->group_id : $this->model->groupId;
        return new Channel('communication.' . $groupId);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'comment.deleted';
    }
}
