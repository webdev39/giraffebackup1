<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDefaultBudget extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:default_budget';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default budget for exist tasks';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        request()->request->add(['isDraftTask', true]);
    }

    /**
     * Execute the console command.
     *
     * @throws \Throwable
     */
    public function handle()
    {
        DB::transaction(function () {
            $tasks  = app('TaskRepo')->findWhere(['budget_id' => null]);

            /** @var Task $task */
            foreach ($tasks as $task) {
                $budget = app('BudgetSer')->createDefaultBudget();

                $task->budget()->associate($budget)->save();
            }
        });
    }
}
