<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;

class PipelineDeactivatedNotification extends BaseNotification
{
    use Queueable;
    private $message;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $message)
    {
        $this->message = $message;
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return array_merge($this->toArray($notifiable), [
            'message'      => $this->message
        ]);
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
            'sender_id'         => '',
            'sender_name'       => 'Bot',
            'sender_last_name'  => '',
            'sender_avatar'     => '',
            'message'           => $this->getMessage(),
        ];
    }
}
