<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RemoveAllDraftTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:draft-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all draft tasks';

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
        try {
            $tasks      = app("TaskRepo")->getDraftTasks();

            $taskIds    = $tasks->pluck('id')->toArray();
            $budgetIds  = $tasks->pluck('budget_id')->filter()->toArray();

            $this->info('Remove draft tasks');

            app("BudgetRepo")->removeBudgetByIds($budgetIds);
            app("NotificationSubscriptionRepo")->removeSubscribersByTaskIds($taskIds);
            app("PersonalDeadlineRepo")->removePersonalDeadlineByTaskIds($taskIds);
            app("UserTenantTaskRepo")->removeByTaskIds($taskIds);
            app("TaskRepo")->removeTaskByIds($taskIds);

            $this->info("Finish. It was remote {$tasks->count()} task");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
