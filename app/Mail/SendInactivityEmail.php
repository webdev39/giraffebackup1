<?php

namespace App\Mail;

use App\Models\UserTenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInactivityEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $absence;
    public $remainder;

    public $user;

    /**
     * SendInactivityEmail constructor.
     *
     * @param UserTenant $userTenant
     * @param int        $absence
     * @param int        $remainder
     */
    public function __construct(UserTenant $userTenant, int $absence, int $remainder)
    {
        $this->absence      = $absence;
        $this->remainder    = $remainder;

        $this->user         = $userTenant->user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Warning of inactivity')->view('emails.inactivity');
    }
}
