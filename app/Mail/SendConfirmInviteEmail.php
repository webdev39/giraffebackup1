<?php

namespace App\Mail;

use App\Models\UserTenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendConfirmInviteEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tenant;
    public $password;
    public $userTenant;
    public $user;

    /**
     * SendConfirmInviteEmail constructor.
     *
     * @param UserTenant  $userTenant
     * @param string|null $password
     */
    public function __construct(UserTenant $userTenant, string $password = null)
    {
        $this->password     = $password;
        $this->userTenant   = $userTenant;
        $this->tenant       = $userTenant->tenant;
        $this->user         = $userTenant->user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Confirmation of registration')->view('emails.invite');
    }
}
