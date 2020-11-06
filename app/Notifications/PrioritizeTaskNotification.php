<?php

namespace App\Notifications;

use App\Http\Resources\NotificationResource;
use App\Models\Board;
use App\Models\Priority;
use App\Models\Task;
use App\Models\User;

class PrioritizeTaskNotification extends BaseNotification
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
        $this->newValue = !empty($newValue) ? Priority::find($newValue)->name : '';
        $this->oldValue = !empty($oldValue) ? Priority::find($oldValue)->name : '';
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
        return __('notifications.task.changed.priority', [
            'sender' => $this->sender->full_name,
            'new'    => $this->newValue,
            'old'    => $this->oldValue,
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
