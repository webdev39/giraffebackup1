<?php

namespace App\Console\Commands;

use App\Mail\SendInactivityEmail;
use App\Models\User;
use App\Models\UserTenant;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MailingAboutInactivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing:inactivity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send emails about tenant inactivity';

    /**
     * The duration of the lack of tenant to which will be sent email
     *
     * @var array
     */
    private $durations = [90, 120, 130, 132, 133, 134, 135];

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
        /** @var UserTenant $tenants */
        $userTenants = app('UserTenantRepo')->with('user')->findWhere(['is_owner' => true]);

        /** @var User $user */
        foreach ($userTenants as $userTenant) {
            $absence   = (new Carbon($userTenant->user->last_activity))->diffInDays();
            $remainder = max($this->durations) - $absence;

            if (in_array($absence, $this->durations)) {
                Mail::to($userTenant->user)->queue(new SendInactivityEmail($userTenant, $absence, $remainder));
            }
        }
    }
}
