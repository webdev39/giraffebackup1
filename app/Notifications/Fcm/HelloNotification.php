<?php

namespace App\Notifications\Fcm;

use Benwilkins\FCM\FcmMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class HelloNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
     * @param $notifiable
     * @return mixed
     */
    public function toFcm($notifiable)
    {
        $message = new FcmMessage();
        $message->content([
            'title'        => 'Hello from OC Firebase!',
            'body'         =>'You are now subscribed to push notifications',
            'icon'         => asset('/images/oclogo-big.png'),
            'url'          => config('app.url').'/profile',
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
        ]);

        return $message;
    }
}
