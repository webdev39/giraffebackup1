<?php

namespace App\Console\Commands;

use App\Models\Board;
use App\Models\Budget;
use App\Models\Group;
use App\Models\Priority;
use App\Models\Task;
use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;

class RemoveUnusedGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:unused-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all groups that are not associated with users';

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
        $groups     = Group::doesntHave('userTenantGroups')->get();
        $groupIds   = $groups->pluck('id')->toArray();

        $boards     = app('BoardRepo')->getBoardsByGroupIds($groupIds);
        $boardIds   = $boards->pluck('id')->unique()->toArray();

        $tasks      = app('TaskRepo')->getTasksByBoardIds($boardIds);
        $taskIds    = $tasks->pluck('id')->unique()->toArray();

        $boardBudgetIds = $boards->pluck('budget_id');
        $taskBudgetIds = $tasks->pluck('budget_id');
        $budgetIds = $boardBudgetIds->concat($taskBudgetIds);

        Group::whereIn('id', $groupIds)->delete();
        Board::whereIn('id', $boardIds)->delete();
        Task::whereIn('id', $taskIds)->delete();

        Activity::where('subject_type', Task::class)
            ->whereIn('subject_id', $taskIds)
            ->delete();

        Budget::whereIn('id', $budgetIds)->delete();

        $this->info('Finish');
    }
}
