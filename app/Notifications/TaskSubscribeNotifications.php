<?php

namespace App\Notifications;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Events\Eloquent\Saved\DeletedNotificationSubscriptionEvent;
use App\Events\Eloquent\Saved\SavedNotificationSubscriptionEvent;
use App\Http\Resources\NotificationResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class TaskSubscribeNotifications extends BaseNotification
{
    /** @var mixed  */
    private $board;

    /** @var Task  */
    private $task;

    /** @var bool */
    private $subscribed;

    /**
     * TaskSubscribeNotifications constructor.
     * @param User $sender
     * @param Task $task
     * @param bool $isUnsubscribed
     */
    public function __construct(User $sender, Task $task, bool $isUnsubscribed)
    {
        $this->task     = $task;
        $this->sender   = $sender;
        $this->board    = $task->board()->first();
        $this->subscribed = !((bool) $isUnsubscribed);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return __('notifications.task.action.subscribe', [
            'action'    => $this->subscribed ? 'subscribed to' : 'unsubscribed from',
            'sender'    => $this->sender->full_name,
            'receiver'  => $this->receiver->full_name,
            'task'      => $this->task->name,
        ]);
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return array_merge(parent::toArray($notifiable), [
            'task_id'      => $this->task->id,
            'task_name'      => $this->task->name,
            'board_id'     => $this->board->id,
            'group_id'     => $this->board->group->id,
        ]);
    }

    /**
     * @param mixed $notifiable
     * @return NotificationResource|array
     */
    public function toArray($notifiable)
    {
        return $this->toNotificationResource($notifiable);
    }
}
