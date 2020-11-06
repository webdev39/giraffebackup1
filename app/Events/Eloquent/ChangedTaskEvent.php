<?php

namespace App\Events\Eloquent;

use App\Models\BaseModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ChangedTaskEvent extends BaseEloquentEvent implements ShouldBroadcastNow
{
    /**
     * @var array
     */
    public $subscribers_to_task;

    /**
     * Create a new event instance.
     *
     * @param BaseModel $model
     */
    public function __construct(BaseModel $model)
    {
        parent::__construct($model);
        $this->subscribers_to_task = $model->load('notifySubscriptions')->notifySubscriptions->pluck('user_id');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('tasks');
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'task.changed';
    }
}