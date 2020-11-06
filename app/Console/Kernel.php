<?php

namespace App\Console;

use App\Console\Commands\CleanupUnattachedFiles;
use App\Console\Commands\ComputingTimerBillings;
use App\Console\Commands\ComputingTimers;
use App\Console\Commands\ConvertTaskChangesToNotifications;
use App\Console\Commands\AdditionalTaskNotifications;
use App\Console\Commands\RemoveAllDraftTasks;
use App\Console\Commands\ReopenTasksMonthBudget;
use App\Console\Commands\ReopenTasksRepeat;
use App\Console\Commands\MailingAboutUnreadNotification;
use App\Console\Commands\MailingAboutInactivity;
use App\Console\Commands\TenantsCreate;
use App\Console\Commands\TenantsList;
use App\Console\Commands\TenantsShowUsers;
use App\Services\Task\TaskService;
use App\Services\TaskChangesQueue;
use App\Services\Timer\TimerService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ComputingTimers::class,
        ComputingTimerBillings::class,

        ReopenTasksRepeat::class,
        ReopenTasksMonthBudget::class,

        RemoveAllDraftTasks::class,

        TenantsList::class,
        TenantsCreate::class,
        TenantsShowUsers::class,

        MailingAboutInactivity::class,
        MailingAboutUnreadNotification::class,

        ConvertTaskChangesToNotifications::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Create tasks from the pipelines mails
        $schedule->command('pipelines:run')->everyTenMinutes();

        // Send emails
        $schedule->job(new MailingAboutInactivity)->dailyAt(config('cron.time.mailing'))->thenPing(config('heartbeats.MailingAboutInactivity'));
        $schedule->job(new MailingAboutUnreadNotification)->dailyAt(config('cron.time.mailing'))->thenPing(config('heartbeats.MailingAboutUnreadNotification'));

        // Computing data
        $schedule->job(new ComputingTimers)->dailyAt(config('cron.time.computing.timers'))->thenPing(config('heartbeats.ComputingTimers'));

        // Repeat tasks
        $schedule->job(new ReopenTasksRepeat)->dailyAt('02:00')->thenPing(config('heartbeats.ReopenTasksRepeat'));
        $schedule->job(new ReopenTasksMonthBudget)->monthlyOn(1, config('cron.time.computing.timers'))->thenPing(config('heartbeats.ReopenTasksMonthBudget'));

        $schedule->job(new CleanupUnattachedFiles())->daily();

        $schedule->job(new ConvertTaskChangesToNotifications(app()->make(TaskChangesQueue::class)))->everyFiveMinutes();

        $schedule->command('backup:clean')->daily()->at('01:00');
        $schedule->command('backup:run')->daily()->at('02:00');

        // Send notifications to the user about tasks with an expired deadline
        $schedule->job(new AdditionalTaskNotifications(new TaskService, new TimerService))->daily()->at('05:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
