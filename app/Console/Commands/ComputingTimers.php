<?php

namespace App\Console\Commands;

use App\Models\Timer;
use App\Services\Time\TimeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ComputingTimers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'computing:timers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Computing for Timers';

    /**
     * Execute the console command.
     *
     * @throws \Throwable
     */
    public function handle()
    {
        $countUpdateTimers = 0;
        $countDeletedPauses = 0;
        $countDeletedLogs = 0;

        \DB::transaction(function () use (&$countUpdateTimers, &$countDeletedPauses, &$countDeletedLogs) {
            $logs = app('TimerLogSer')->getTimerLog();

            foreach ($logs as $log) {
                if (is_null($log->timer)) {
                    app('TimerLogSer')->removeLog($log->id);

                    $countDeletedLogs += 1;
                } else {
                    $trackedTime = TimeService::getSumTimeByTimer($log->timer)->format('%H:%I:%S');

                    $timer = app('TimerSer')->createOrUpdateTimer([
                        'time'      => $trackedTime,
                        'logged_at' => $log->timer->end_time,
                    ], $log->timer->id);

                    $countUpdateTimers  += 1;
                    $countDeletedPauses += app('TimerSer')->deletePausesByTimerId($timer->id);

                    app('BillingSer')->updateOrCreateTimerBilling($timer);
                }
            }
        });
    }
}
