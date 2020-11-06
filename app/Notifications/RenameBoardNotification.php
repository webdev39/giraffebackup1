<?php

namespace App\Notifications;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Http\Resources\NotificationResource;
use App\Models\Board;
use App\Models\User;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class RenameBoardNotification extends BaseNotification
{
    /** @var Board */
    private $board;

    /**
     * RenameBoardNotification constructor.
     *
     * @param BaseEloquentEvent $event
     * @param Board             $board
     * @param string            $field
     */
    public function __construct(BaseEloquentEvent $event, Board $board, string $field)
    {
        $this->board    = $board;
        $this->sender   = $event->user;
        $this->options  = $event->notify[$field];
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return __('notifications.board.changed.name', [
            'sender' => $this->sender->full_name,
            'new'    => $this->options['new_value'],
            'old'    => $this->options['old_value'],
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