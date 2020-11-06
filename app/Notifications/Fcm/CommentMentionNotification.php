<?php

namespace App\Notifications\Fcm;

use App\Events\CreatedCommentEvent;
use App\Notifications\BaseNotification;
use Benwilkins\FCM\FcmMessage;

class CommentMentionNotification extends BaseNotification
{
    /** @var int */
    private $groupId;

    /** @var \App\Models\Board */
    private $board;

    /** @var \App\Models\Task */
    private $task;

    /** @var string */
    private $comment;

    /**
     * CommentMentionNotification constructor.
     *
     * @param CreatedCommentEvent $event
     */
    public function __construct(CreatedCommentEvent $event)
    {
        $this->task     = $event->task;
        $this->comment  = $event->comment;
        $this->sender   = $event->user;
        $this->groupId  = $event->groupId;
        $this->board    = isset($this->task) ? $this->task->board()->first() : null;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['fcm'];
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return __('notifications.comment.create', [
            'sender'  => $this->sender->full_name,
            'comment' => strip_tags($this->comment)
        ]);
    }

    /**
     * @param $notifiable
     * @return mixed
     */
    public function toFcm($notifiable)
    {
        $message = new FcmMessage();
        $message->content([
            'title'        => 'New Mention',
            'body'         => $this->getMessage(),
            'icon'         => asset('/images/oclogo-big.png'),
            'url'          => config('app.url').'/profile',
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
        ]);

        return $message;
    }
}
