<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class SendMissedNotificationsEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $notify;
    public $tasks;
    public $user;

    /**
     * SendMissedNotificationsEmail constructor.
     *
     * @param User       $user
     * @param Collection $notify
     * @param Collection $tasks
     */
    public function __construct(User $user, Collection $notify, Collection $tasks)
    {
        $this->notify = $notify;
        $this->tasks = $tasks;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.missed_notifications');
    }
}
