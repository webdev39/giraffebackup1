<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class RemoveUnusedTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:unused-tenants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete tenants and all data, provided there are no users (they have been deleted, etc.)';

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
        $tenants = Tenant::doesntHave('userTenants')->get();

        /** @var Tenant $tenant */
        foreach ($tenants as $tenant) {
            $tenant->customers()->delete();
            $tenant->subscription()->delete();
            $tenant->pipelines()->delete();
            $tenant->customRoles()->delete();
            $tenant->customPriorities()->delete();
            $tenant->delete();
        }
    }
}
