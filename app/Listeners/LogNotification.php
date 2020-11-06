<?php


namespace App\Listeners;


use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Log;

class LogNotification
{

    /**
     * Handle the event.
     *
     * @param  NotificationSent  $event
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        Log::info(json_encode([
            'channel' => $event->channel,
            'notifiable' => json_encode($event->notifiable),
            'notification' => json_encode($event->notification),
            'response' => json_encode($event->response)
        ]));
    }

}