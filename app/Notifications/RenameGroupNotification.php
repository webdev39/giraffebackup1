<?php

namespace App\Notifications;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Http\Resources\NotificationResource;
use App\Models\Group;
use App\Models\User;

class RenameGroupNotification extends BaseNotification
{
    /** @var Group */
    private $group;

    /**
     * RenameGroupNotification constructor.
     *
     * @param BaseEloquentEvent $event
     * @param Group             $group
     * @param string            $field
     */
    public function __construct(BaseEloquentEvent $event, Group $group, string $field)
    {
        $this->group    = $group;
        $this->sender   = $event->user;
        $this->options  = $event->notify[$field];
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return __('notifications.group.changed.name', [
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
            'group_id'     => $this->group->id,
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