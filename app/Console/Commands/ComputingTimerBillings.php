<?php

namespace App\Console\Commands;

use App\Models\Timer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ComputingTimerBillings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'computing:timer_billings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Computing for Timer Billings';

    /**
     * Execute the console command.
     *
     * @throws \Throwable
     */
    public function handle()
    {
        $countUpdate = 0;
        $countMissed = 0;

        $this->info('Start computing for Timer Billings');

        DB::transaction(function () use (&$countUpdate, &$countMissed) {
            $timers  = app('TimerRepo')->all();

            /** @var Timer $timer */
            foreach ($timers as $timer) {
                $result = app('BillingSer')->updateOrCreateTimerBilling($timer);

                if ($result) {
                    $countUpdate++;
                } else {
                    $countMissed++;
                }
            }
        });

        $this->info("{$countUpdate} timers billing recalculated");
        $this->info("{$countMissed} timers billing missed");
    }
}
