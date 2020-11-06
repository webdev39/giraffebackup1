<?php

use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    /**
     * @throws Throwable
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        \Illuminate\Database\Eloquent\Model::reguard();

        DB::transaction(function () use ($faker) {
            /** @var User $user */
            $user = app('AuthSer')->createUser([
                "name"          => $faker->firstName,
                "last_name"     => $faker->lastName,
                'email'         => 'qwer@qwer.qwer',
                'password'      => 'secret'
            ]);

            $userTenant = app('AuthSer')->createTenant('company_name', 'GiraffeSoftware', $user->id);

            app('TenantSer')->markTenantAsChosen($user->id, $userTenant->tenant_id);

            app('AuthSer')->markUserAsConfirmed($user->id);
            app('AuthSer')->attachToUserTenantRole($userTenant, Role::whereName(Role::OWNER_ROLE['name'])->pluck('id'), true);

            /** @var Customer $customer */
            $userTenant->tenant->customers()->saveMany(
                factory(App\Models\Customer::class, 10)->make(['status' => 'active'])
            );

            event(new \App\Events\UserTenantHasBeenCreated($userTenant, true));
        });
    }
}
