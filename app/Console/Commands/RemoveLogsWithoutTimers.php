<?php

namespace App\Console\Commands;

use App\Models\Log;
use Illuminate\Console\Command;

class RemoveLogsWithoutTimers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:logs-without-timers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Logs without Timer';

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
     * @throws \Exception
     */
    public function handle()
    {
        $logs = app('LogRepo')->getLogsWithoutTimers();
        $logIds = $logs->pluck('id')->toArray();

        $count = Log::whereIn('id', $logIds)->delete();

        $this->info("It was deleted: {$count} records");
    }
}
