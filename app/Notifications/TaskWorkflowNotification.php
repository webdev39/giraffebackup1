<?php

namespace App\Notifications;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Http\Resources\NotificationResource;
use App\Models\Board;
use App\Models\Task;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class TaskWorkflowNotification extends BaseNotification
{
    /** @var Board */
    private $board;

    /** @var Task */
    private $task;

    /**
     * TaskWorkflowNotification constructor.
     * @param User $sender
     * @param Task $task
     */
    public function __construct(User $sender, Task $task)
    {
        $this->task     = $task;
        $this->sender   = $sender;
        $this->board    = $task->board()->first();
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return __('notifications.task.changed.done_by', [
            'sender' => $this->sender->full_name,
            'action' => $this->task->done_by ? 'closed' : 'opened',
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