<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class TenantsList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List existing tenants in a table';

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
        $headers = ['#', 'Company Name', 'Project Name', 'Created At', 'Updated At', 'User Count'];
        $tenants = Tenant::get();
        $tenants = $tenants->map(function ($item) {
            $item->count = $item->users()->count();
            return $item;
        });
        $this->table($headers, $tenants);
    }
}
