<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class PersonalEvents implements ShouldBroadcast
{
    use SerializesModels;

    /** @var string */
    public $event;

    /** @var Model */
    public $model;

    /** @var User */
    public $user;

    /**
     * The name of the queue on which to place the event.
     *
     * @var string
     */
    public $broadcastQueue = 'events';

    /**
     * Create a new event instance.
     *
     * @param string $event
     * @param User   $user
     * @param Model  $model
     */
    public function __construct(string $event, Model $model, User $user)
    {
        $this->event = $event;
        $this->model = $model;
        $this->user  = $user;
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return $this->event;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        if($this->event == 'timer.update' && !empty($this->model->task)) {
            $groupId = $this->model->task->board->first()->group->id;
            return new Channel('communication.' . $groupId);
        }
        return new PrivateChannel('user.'.$this->user->id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id'        => $this->model->id,
            'user_id'   => $this->user->id,
        ];
    }
}