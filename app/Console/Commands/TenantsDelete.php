<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Repositories\TenantRepositoryEloquent;
use App\Services\Tenant\TenantService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TenantsDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:delete {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete tenant it related records';
    /**
     * @var TenantRepositoryEloquent
     */
    private $tenantRepositoryEloquent;
    /**
     * @var TenantService
     */
    private $tenantService;

    /**
     * TenantsDelete constructor.
     * @param TenantRepositoryEloquent $tenantRepositoryEloquent
     * @param TenantService $tenantService
     */
    public function __construct(TenantRepositoryEloquent $tenantRepositoryEloquent, TenantService $tenantService)
    {
        parent::__construct();
        $this->tenantRepositoryEloquent = $tenantRepositoryEloquent;
        $this->tenantService = $tenantService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $tenant = $this->tenantRepositoryEloquent->findOrFail($this->argument('id'));
            if($this->confirm('Do you really want to delete this tenant?')) {
                $this->tenantService->deleteTenant($tenant);
                $this->info('Tenant successfully deleted');
            }
        } catch (ModelNotFoundException $e) {
            $this->error('Tenant was not found');
        }
    }
}
