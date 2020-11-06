<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendConfirmRegistrationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $emailView;
    public $user;
    public $tenant;
    public $inviteHash;

    /**
     * SendConfirmRegistrationEmail constructor.
     * @param string $emailView
     * @param User $user
     */
    public function __construct(string $emailView, User $user)
    {
        $this->emailView = $emailView;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->user;
        Mail::send(
            $this->emailView,
            [
                'user' => $this->user
            ],
            function ($m) use ($user) {
            $m->to($user->email, $user->name . ' ' . $user->last_name)->subject('Confirmation of registration');
        });
    }
}
