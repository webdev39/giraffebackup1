<?php

namespace App\Notifications;

use App\Http\Resources\NotificationResource;
use App\Models\Board;
use App\Models\Task;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class RenameTaskNotification extends BaseNotification
{
    /** @var Board */
    private $board;

    /** @var Task */
    private $task;
    /**
     * @var string
     */
    private $newValue;
    /**
     * @var string
     */
    private $oldValue;

    /**
     * RenameTaskNotification constructor.
     * @param User $sender
     * @param Task $task
     * @param string $newValue
     * @param string $oldValue
     */
    public function __construct(User $sender, Task $task, string $newValue, string $oldValue)
    {
        $this->task     = $task;
        $this->sender   = $sender;
        $this->newValue = $newValue;
        $this->oldValue = $oldValue;
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
        if (is_null($this->oldValue)) {
            return [];
        }

        return ['database', 'broadcast'];
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return __('notifications.task.changed.name', [
            'sender' => $this->sender->full_name,
            'new'    => $this->newValue,
            'old'    => $this->oldValue,
        ]);
    }

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
