<?php

namespace App\Console\Commands;

use App\Models\Budget;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveUnusedBudgets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:unused-budgets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove unused budgets';

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
        $budgets = app('BudgetRepo')->getUnusedBudgets();
        $budgetIds = $budgets->pluck('id')->toArray();

        $count = Budget::whereIn('id', $budgetIds)->delete();

        $this->info("It was deleted: {$count} records");
    }
}
