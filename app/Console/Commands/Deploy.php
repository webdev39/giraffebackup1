<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Deploy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy';

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
        \Artisan::call('migrate');

        $this->info('Clear all cache');

        \Artisan::call('remove:draft-tasks');
        \Artisan::call('remove:unused-budgets');

        \Artisan::call('computing:timers');
        \Artisan::call('computing:timer_billings');

        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('clockwork:clean');

        $this->info('Pleas run');
        $this->warn('sudo service supervisor restart');

        $this->warn('sudo chown -R www-data:www-data $PWD');
        $this->warn('sudo chmod -R 775 $PWD/storage');
    }
}
