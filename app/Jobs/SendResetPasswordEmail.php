<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendResetPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string $emailView
     */
    public $emailView;
    /**
     * @var User $user
     */
    public $user;
    /**
     * @var string $token
     */
    public $token;

    /**
     * SendResetPasswordEmail constructor.
     * @param string $emailView
     * @param User $user
     * @param string $token
     */
    public function __construct($emailView, User $user, $token)
    {
        $this->emailView = $emailView;
        $this->user = $user;
        $this->token = $token;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->user;
        Mail::send($this->emailView, ['user' => $user, 'resetToken' => $this->token], function ($m) use ($user) {
            $m->to($user->email, $user->name . ' ' . $user->last_name)->subject('Confirmation of pass restoring');
        });
    }
}
