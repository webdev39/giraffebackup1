<?php

namespace App\Console\Commands;

use App\Mail\SendMissedNotificationsEmail;
use App\Models\User;
use App\Models\UserTenant;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailingAboutUnreadNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send emails about unread notifications';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::active()->get();

        /** @var User $user */
        foreach ($users as $user) {
            // Check whether the user subscribed to type notifications
            if ($user->notificationType()->where('name', 'email')->exists()) {

                try { // Temporarily,   $userTenant = $user->getChosenTenant(); throws an error
                    /** @var UserTenant $userTenant */
                    $userTenant = $user->getChosenTenant();
                    if(!$userTenant) {
                        continue;
                    }

                    $tasks  = app('UserTaskSer')->getUserTaskWithRelationsActivity('today', $userTenant->id);
                    $notify = $user->unreadNotifications;

                    if ($tasks->count() || $notify->count()) {
                        Mail::to($user)->queue(new SendMissedNotificationsEmail($user, $notify, $tasks));
                    }
                } catch (\Exception $exception) {
                    Log::error('Error getting tenant for', [json_encode($user)]);
                }


            }
        }
    }
}
