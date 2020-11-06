<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Collection;

class CreatedCommentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var string  */
    public $comment;

    /** @var User  */
    public $user;

    /** @var array  */
    public $mentionIds;

    /** @var Task  */
    public $task;

    /** @var integer  */
    public $groupId;

    /**
     * CreatedCommentEvent constructor.
     *
     * @param User $creator
     * @param array $mentions
     * @param string $comment
     * @param Task $task
     * @param int $groupId
     */
    public function __construct(User $creator, array $mentions, string $comment, int $groupId, ?Task $task = null)
    {
        $this->task         = $task;
        $this->user         = $creator;
        $this->comment      = $comment;
        $this->mentionIds   = $mentions;
        $this->groupId      = $groupId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
