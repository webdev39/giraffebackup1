<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use DB;
use Faker\Factory;
use Illuminate\Console\Command;

class TenantsCreateDummy extends Command
{
    public $email;
    protected $signature = 'tenants:create-dummy {--email=}';
    protected $description = 'Create a new tenant with some dummy data';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $faker = \Faker\Factory::create();

        \Illuminate\Database\Eloquent\Model::reguard();

        DB::transaction(function () use ($faker) {
            /** @var User $user */
            $user = app('AuthSer')->createUser([
                "name"          => $faker->firstName,
                "last_name"     => $faker->lastName,
                'email'         => $this->option('email'),
                'password'      => 'secret'
            ]);

            $userTenant = app('AuthSer')->createTenant('company_name', 'GS Software', $user->id);

            app('TenantSer')->markTenantAsChosen($user->id, $userTenant->tenant_id);

            app('AuthSer')->markUserAsConfirmed($user->id);
            app('AuthSer')->attachToUserTenantRole($userTenant, Role::whereName(Role::OWNER_ROLE['name'])->pluck('id'), true);

            /** @var Customer $customer */
            $userTenant->tenant->customers()->saveMany(
                factory(\App\Models\Customer::class, 10)->make(['status' => 'active'])
            );

            event(new \App\Events\UserTenantHasBeenCreated($userTenant, true));
        });
        $this->info('User created');
        $this->info('Email: '.$this->option('email'));
        $this->info('Password: secret');
    }
}
