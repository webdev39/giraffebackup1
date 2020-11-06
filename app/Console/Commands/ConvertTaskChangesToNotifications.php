<?php

namespace App\Console\Commands;

use App\Services\TaskChangesQueue;
use Illuminate\Console\Command;

class ConvertTaskChangesToNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task_changes:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var TaskChangesQueue
     */
    private $taskChangesQueue;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TaskChangesQueue $taskChangesQueue)
    {
        parent::__construct();
        $this->taskChangesQueue = $taskChangesQueue;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->taskChangesQueue->convertChangesToNotifications();
    }
}
