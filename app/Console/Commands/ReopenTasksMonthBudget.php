<?php

namespace App\Console\Commands;

use App\Models\Repeat;
use App\Models\Task;
use App\Services\ActivityLog\ActivityLogService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ReopenTasksMonthBudget extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:reopen_month_budget';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reopen tasks with monthly budget';

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
            $tasks = app('TaskRepo')->getDoneTasksByMonthlyBudget();
            $taskIds = $tasks->pluck('id')->toArray();

            if ($tasks) {
                app('TaskRepo')->updateWhereIn('id', $taskIds, ['done_by' => null]);

                /** @var Repeat $repeat */
                foreach ($tasks as $task) {
                    ActivityLogService::customTaskAction(Task::createFromStd($task), null, $this->description,'reopen','done_by');
                }
            }
        });
    }
}
