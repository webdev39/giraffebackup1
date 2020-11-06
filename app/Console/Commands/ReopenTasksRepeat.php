<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Models\Repeat;
use App\Services\ActivityLog\ActivityLogService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ReopenTasksRepeat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:repeat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reopen tasks according to condition';

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
     * @throws \Throwable
     */
    public function handle()
    {
        \DB::transaction(function () {
            $repeats = app('RepeatRepo')->with('task')->all();
            $currentRepeats = collect();

            /** @var Repeat $repeat */
            foreach ($repeats as $repeat) {
                $ended = Carbon::parse($repeat->ended_at);
                $diff = now()->diffInHours($ended, false);

                if ($diff < 0) {
                    return app('RepeatRepo')->delete($repeat->id);
                }

                $interval = CarbonInterval::{$repeat->time_unit}($repeat->time_interval);

                $next = Carbon::parse($repeat->repeated_at ?? $repeat->started_at)->add($interval);
                $diff = now()->diffInHours($next, false);

                if ($diff < 0 && !is_null($repeat->task->done_by)) {
                    $currentRepeats->push($repeat);
                }
            }

            if ($currentRepeats) {
                /** @var Repeat $repeat */
                foreach ($currentRepeats as $repeat) {
                    $repeat->task()->update(['done_by' => null]);
                    $repeat->update(['repeated_at' => now()->startOfDay()]);

                    $message = 'According to my terms, the task was reopened.';
                    ActivityLogService::customTaskAction($repeat->task, null, $message,'reopen','done_by');
                }
            }
        });
    }
}
