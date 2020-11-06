<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class TaskChangeToNotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var Task
     */
    public $task;
    /**
     * @var array
     */
    public $subscribers;
    /**
     * @var string
     */
    public $field;
    /**
     * @var string
     */
    public $notificationClass;
    /**
     * @var string
     */
    public $newValue;
    /**
     * @var string
     */
    public $oldValue;
    /**
     * @var User
     */
    public $sender;

    /**
     * TaskChangeToNotificationEvent constructor.
     * @param User $sender
     * @param Task $task
     * @param array $subscribers
     * @param string $field
     * @param string $notificationClass
     * @param string $newValue
     * @param string $oldValue
     */
    public function __construct(User $sender, Task $task, array $subscribers, string $field, string $notificationClass, string $newValue, string $oldValue)
    {
        $this->task = $task;
        $this->subscribers = $subscribers;
        $this->field = $field;
        $this->notificationClass = $notificationClass;
        $this->newValue = $newValue;
        $this->oldValue = $oldValue;
        $this->sender = $sender;
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
