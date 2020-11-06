<?php

namespace App\Notifications;

use App\Channels\CustomFcmChannel;
use App\Models\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

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
        return [WebPushChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Hello from OC!',
            'body' => 'You are now subscribed to push notifications.',
            'action_url' => config('app.url'),
            'created' => Carbon::now()->toIso8601String()
        ];
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
        $url = config('app.url').'/profile';

        $message = (new WebPushMessage)
            ->title('Hello from OC!')
            ->icon('/images/oclogo-big.png')
            ->body('You are now sbuscribed to push notifications')
            ->action('View app', $url)
            ->data(['id' => $notification->id, 'url' => $url]);

        return $message;
    }

    public function toFcm()
    {

    }
}
