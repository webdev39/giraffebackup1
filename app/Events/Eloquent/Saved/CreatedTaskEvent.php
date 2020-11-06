<?php

namespace App\Events\Eloquent\Saved;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Models\BaseModel;
use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

/**
 * Class CreatedTaskEvent
 *
 * @package App\Events\Eloquent
 */
class CreatedTaskEvent extends BaseEloquentEvent implements ShouldBroadcastNow
{
    /** @var Task */
    public $model;

    /** @var int */
    public $draft;

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

        $this->draft = $model->getOriginal('draft');
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
        return 'task.created';
    }
}