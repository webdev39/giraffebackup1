<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultRoles = [
            Role::SUPER_ADMIN_ROLE,
            Role::OWNER_ROLE,
            Role::ADMIN_ROLE,
            Role::MEMBER_ROLE,
            Role::MEMBER_ROLE_1,
            Role::EXTERNAL_ROLE,
            Role::GROUP_LEADER_ROLE,
            Role::GROUP_MEMBER_ROLE,
            Role::CUSTOM_ROLE,
            Role::PRIVILEGED_MEMBER_ROLE,
            Role::PRIVILEGED_MEMBER_ROLE_2,
            Role::PRIVILEGED_MEMBER_ROLE_3
        ];

        foreach ($defaultRoles as $defaultRole) {
            if (!$role = Role::where('name', $defaultRole['name'])->first()) {
                /** @var Role $role */
                $role               = new Role();
                $role->name         = $defaultRole['name'];
                $role->display_name = $defaultRole['display_name'];
                $role->description  = $defaultRole['description'];
                $role->is_default   = 1;
                $role->save();
            }

            foreach ($defaultRole['permissions'] as $rolePermission) {
                /** @var Permission $permission */
                $permission = Permission::where('name', $rolePermission['name'])->first();

                if (!$permission) {
                    $permission               = new Permission();
                    $permission->name         = $rolePermission['name'];
                    $permission->display_name = $rolePermission['display_name'];
                    $permission->description  = $rolePermission['description'];
                    $permission->save();
                }

                if (!$role->hasPermission($permission->name)) {
                    try {
                        $role->attachPermission($permission);
                    } catch (Exception $e) {
                        //
                    }
                }
            }
        }
    }
}
