<?php


use App\Models\User;
use App\Models\UserTenantGroup;
use Illuminate\Database\Seeder;

class AddOwnerToGroupSeeder extends Seeder
{
    /**
     * @throws Throwable
     */
    public function run()
    {
        $userTenantGroup = new UserTenantGroup;
        $userTenantGroup->forceFill([
            'user_tenant_id' => 1,
            'group_id' => 1,
            'is_creator' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $userTenantGroup->save();

        \Illuminate\Support\Facades\DB::table('user_tenant_group_role')
            ->insert([
                'user_tenant_group_id' => $userTenantGroup->id,
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ]);
    }
}