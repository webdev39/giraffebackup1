<?php

namespace App\Notifications;

use App\Events\CreatedCommentEvent;
use App\Http\Resources\NotificationResource;
use Benwilkins\FCM\FcmMessage;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

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
        return ['database', 'broadcast', WebPushChannel::class];
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return __('notifications.comment.create', [
            'sender'  => $this->sender->full_name,
            'comment' => $this->comment
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
            'task_id'      => $this->task ? $this->task->id : '',
            'task_name'    => $this->task ? $this->task->name : '',
            'board_id'     => $this->board ? $this->board->id : '',
            'group_id'     => $this->groupId,
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
        // A bit of a hack. I didn't yet succeed in extracting payload in social js, so I get it from action
        $url = config('app.url').'/group/'.$this->board->group->id.'/board/'.$this->board->id.'?taskId='.$this->task->id;


        return (new WebPushMessage)
            ->title('New Mention')
            ->icon('/images/oclogo-big.png')
            ->body(strip_tags($this->comment))
            ->data(['id' => $notification->id, 'url' => $url])
            ->action('View app', $url);
    }
}
