<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CreateUniqPriority extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:uniq_priority';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create uniq default priority for boards';

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
        /** @var Collection $boards */
        $boards     = app('BoardRepo')->all();
        $boardIds   = $boards->pluck('id')->toArray();

        /** @var Collection $tasks */
        $tasks      = app('TaskRepo')->getTasksByBoardIds($boardIds);
        $priorities = app('PrioritySer')->getDefaultPriority();

        $boards->each(function($board) use ($priorities, $tasks) {
            DB::transaction(function () use ($board, $priorities, $tasks) {
                $priorities->each(function($priority) use ($board, $tasks) {
                    $new = app('PrioritySer')->createOrUpdatePriority([
                        'board_id'   => $board->id,
                        'name'       => $priority->name,
                        'color'      => $priority->color,
                        'is_primary' => $priority->is_primary,
                    ]);

                    $ids = $tasks->where('board_id', $board->id)->where('priority_id', $priority->id)->pluck('id')->toArray();

                    app('TaskRepo')->updatePriority($ids, $new->id);
                });
            });
        });

        $this->info("run: php artisan remove:draft-tasks");
    }
}
