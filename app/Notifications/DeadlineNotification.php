<?php

namespace App\Notifications;

use App\Models\Board;
use App\Models\Task;
use App\Models\User;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use App\Http\Resources\NotificationResource;

class DeadlineNotification extends BaseNotification
{

    /**
     * @var string
     */
    protected $sender;

    /**
     * @var Task
     */
    private $task;

    /**
     * @var Board
     */
    private $board;

    /**
     * @var User
     */
    protected $receiver;

    /**
     * DeadlineNotification constructor.
     * @param $sender
     * @param Task $task
     * @param User $receiver
     */
    public function __construct($sender, Task $task, User $receiver)
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
        return ['database', 'broadcast', WebPushChannel::class];
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return __('notifications.task.deadline', [
            'task' => $this->task->name
        ]);
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
            'task_id'       => $this->task->id,
            'task_name'     => $this->task->name,
            'board_id'      => $this->board->id,
            'group_id'      => $this->board->group->id,
            'receiver_id'   => $this->receiver->id,
            'receiver_name' => $this->receiver->full_name,
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
            ->title(__('notifications.web_push.deadline.title'))
            ->icon('/images/oclogo-big.png')
            ->body(__('notifications.web_push.deadline.message'))
            ->action('View app', $url);
    }
}
