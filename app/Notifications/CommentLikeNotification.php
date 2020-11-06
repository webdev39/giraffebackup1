<?php


namespace App\Notifications;


use App\Events\LikedCommentEvent;
use App\Http\Resources\NotificationResource;
use App\Models\Comment;
use App\Models\User;

class CommentLikeNotification extends BaseNotification
{
    /**
     * @var Comment
     */
    private $model;
    /**
     * @var Comment
     */
    private $comment;
    /**
     * @var \App\Models\Task
     */
    private $task;
    private $board;

    public function __construct(LikedCommentEvent $event)
    {
        $this->task     = $event->comment->task;
        $this->comment  = $event->comment;
        $this->sender   = $event->user;
        $this->board    = $this->task ? $this->task->board()->first(): null;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return __('notifications.comment.like', [
            'sender'  => $this->sender->full_name,
            'comment' => $this->comment->body
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
            'task_id'      => optional($this->task)->id,
            'task_name'      => optional($this->task)->name,
            'board_id'     => optional($this->board)->id,
            'group_id'     => $this->comment->groupId,
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