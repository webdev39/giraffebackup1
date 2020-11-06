<?php

namespace App\Notifications;

use App\Events\GroupCommentPostedEvent;
use App\Http\Resources\NotificationResource;
use App\Models\Comment;
use NotificationChannels\WebPush\WebPushMessage;

class GroupCommentPostedNotification extends BaseNotification
{

    /** @var string */
    private $comment;
    /**
     * @var Comment
     */
    private $model;

    /**
     * GroupCommentPostedNotification constructor.
     * @param GroupCommentPostedEvent $event
     */
    public function __construct(GroupCommentPostedEvent $event)
    {
        $this->model = $event->comment;
        $this->comment  = $event->comment->body;
        $this->sender   = $event->user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
            'group_id'     => $this->model->groupId,
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
            ->action('View app', $url);
    }


}
