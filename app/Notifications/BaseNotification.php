<?php

namespace App\Notifications;

use App\Http\Resources\NotificationResource;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Class BaseNotification
 *
 * @package App\Notifications
 */
abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $receiver;
    protected $sender;
    protected $options;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return '';
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        if (!$this->receiver) {
            $this->receiver = $notifiable;
        }

        return [
            'id'                => $this->receiver->id,
            'sender_id'         => $this->sender->id,
            'sender_name'       => $this->sender->name,
            'sender_last_name'  => $this->sender->last_name,
            'sender_avatar'     => $this->sender->avatar,
            'message'           => $this->getMessage(),
        ];
    }

    /**
     * @param $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        $timestamp = Carbon::now()->addSecond()->toDateTimeString();

        return new BroadcastMessage([
            'notifiable_id'     => $notifiable->id,
            'notifiable_type'   => get_class($notifiable),
            'data'              => $this->toArray($notifiable),
            'read_at'           => null,
            'created_at'        => $timestamp,
            'updated_at'        => $timestamp,
        ]);
    }

    public function actionType()
    {
        return $this->type;
    }



    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toNotificationResource($notifiable)
    {
        $notification = (new DatabaseNotification);
        $notification->data = $this->toDatabase($notifiable);
        $notification->notifiable = $this->receiver;
        $notification->created_at = now();
        $notification->type = static::class;

        return new NotificationResource($notification);
    }
}
