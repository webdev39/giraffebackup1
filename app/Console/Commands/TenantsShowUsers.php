<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class TenantsShowUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:showUsers  {company? : The name of the company}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show tenant\'s users';

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
        $companyName = $this->argument('company') ?? $this->ask('Which company?');
        $tenant = Tenant::whereRaw("UPPER(`company_name`) LIKE '%". strtoupper($companyName)."%'")->first();
        $users = $tenant->users;

        $users = $users->map(function($item) {
            return [
                'name' => $item->name.' '.$item->last_name,
                'email' => $item->email,
                'nickname' => $item->nickname,
                'is_confirmed' => $item->is_confirmed ? 'confirmed' : 'unconfirmed',
            ];
        });

        $headers = ['Name', 'Email', 'Nickname', 'Is confirmed'];
        $this->table($headers, $users);

    }
}
