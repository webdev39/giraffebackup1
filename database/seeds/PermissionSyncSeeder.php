<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSyncSeeder extends Seeder
{

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
            /** @var Role $role */
            if ($role = Role::where('name', $defaultRole['name'])->first()) {
                $role->perms()->detach();

                foreach ($defaultRole['permissions'] as $rolePermission) {
                    /** @var Permission $permission */
                    if ($permission = Permission::where('name', $rolePermission['name'])->first()) {
                        if (!$role->hasPermission($permission->name)) {
                            $role->attachPermission($permission);
                        }
                    }
                }
            }
        }
    }
}
