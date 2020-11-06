<?php

namespace App\Notifications;

use App\Http\Resources\NotificationResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TaskSubscriptionAndAssignmentNotification extends BaseNotification
{
    /**
     * @var Task
     */
    private $task;
    private $board;

    /**
     * TaskSubscriptionAndAssignmentNotification constructor.
     * @param User $sender
     * @param Task $task
     * @param User $receiver
     */
    public function __construct(User $sender, Task $task, User $receiver)
    {
        $this->task     = $task;
        $this->sender   = $sender;
        $this->board    = $task->board()->first();
        $this->receiver = $receiver;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($this->sender->id !== $this->receiver->id) {
            return ['database', 'broadcast', WebPushChannel::class];
        }

        return [];
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return '';
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return array_merge(parent::toArray($notifiable), [
            'task_id'      => $this->task->id,
            'task_name'      => $this->task->name,
            'board_id'     => $this->board->id,
            'group_id'     => $this->board->group->id,
            'receiver_id'      => $this->receiver->id,
            'receiver_name'      => $this->receiver->full_name,
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

    /**
     * Get the web push representation of the notification.
     *
     * @param  mixed  $notifiable
     * @param  mixed  $notification
     * @return \Illuminate\Notifications\Messages\DatabaseMessage|\NotificationChannels\WebPush\WebPushMessage
     */
    public function toWebPush($notifiable, $notification)
    {
        $url = config('app.url').'/group/'.$this->board->group->id.'/board/'.$this->board->id.'?taskId='.$this->task->id;
        return (new WebPushMessage)
            ->title('Task assignment')
            ->icon('/images/oclogo-big.png')
            ->body('You was assigned to the task "'.$this->task->name.'"')
            ->action('View app', $url);
    }
}
