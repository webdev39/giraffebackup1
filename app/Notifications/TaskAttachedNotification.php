<?php

namespace App\Notifications;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Events\Eloquent\Deleted\DeletedUserTenantTaskEvent;
use App\Events\Eloquent\Saved\SavedUserTenantTaskEvent;
use App\Http\Resources\NotificationResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TaskAttachedNotification extends BaseNotification
{
    /** @var mixed  */
    private $board;

    /** @var Task  */
    private $task;

    /** @var bool */
    private $attach;

    /** @var bool */
    private $detach;

    /**
     * TaskAttachedNotification constructor.
     * @param User $sender
     * @param Task $task
     * @param $attach
     * @param $detach
     */
    public function __construct(User $sender, Task $task, $attach, $detach)
    {
        $this->task     = $task;
        $this->attach   = $attach;
        $this->detach   = $detach;
        $this->sender   = $sender;
        $this->board    = $task->board()->first();

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($this->sender->id !== $notifiable->id) {
            return ['database', 'broadcast', WebPushChannel::class];
        }

        return [];
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return __('notifications.task.action.attach', [
            'action'    => $this->attach ? 'assigned' : 'unassigned',
            'sender'    => $this->sender->full_name,
            'receiver'  => $this->getReceiverFullName(),
            'task'      => $this->task->name,
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
            'receiver_name' => $this->getReceiverFullName(),
            'receiver_id'   => $this->attach ? $this->attach : $this->detach,
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

    /**
     * Get receiver
     *
     * @return string | User
     */
    public function getReceiver()
    {
        $id = (int) ($this->attach ? $this->attach : $this->detach);
        $receiver = User::where('id', $id)->first();
        if (! is_null($receiver)) {
            return $receiver;
        }
        return '';
    }

    /**
     * Get receiver
     *
     * @return string
     */
    public function getReceiverFullName()
    {
        if ($this->getReceiver() !== '') {
            return $this->getReceiver()->full_name;
        }
        return '';
    }
}
