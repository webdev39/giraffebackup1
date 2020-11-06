<?php

namespace App\Console\Commands;

use App\Models\Priority;
use Illuminate\Console\Command;

class RemoveUnusedPriorities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:unused-priorities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all priorities that are not associated with boards';

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
        Priority::doesntHave('board')->delete();
        $this->info('Finish');
    }
}
