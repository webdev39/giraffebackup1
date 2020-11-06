<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

class AddCanInviteRole extends Migration
{
    /**
     * @throws Throwable
     */
    public function up()
    {
        DB::transaction(function () {
            if(!$perm = Permission::whereName(Permission::CAN_INVITE_OTHERS_PERMISSIONS['name'])->first()) {
                $perm = new Permission();
                $perm->name = Permission::CAN_INVITE_OTHERS_PERMISSIONS['name'];
                $perm->display_name = Permission::CAN_INVITE_OTHERS_PERMISSIONS['display_name'];
                $perm->description = Permission::CAN_INVITE_OTHERS_PERMISSIONS['description'];
                $perm->save();
            }

            /** @var Role $role */
            if(!$role = Role::whereName(Permission::CAN_INVITE_OTHERS_PERMISSIONS['name'])->first()) {
                $role = Role::create(array_merge(Permission::CAN_INVITE_OTHERS_PERMISSIONS, [
                    'is_one_permission' => true
                ]));
            }

            $role->update(['is_one_permission' => true]);

            $role->perms()->sync([$perm->id]);
        });
    }

    /**
     * @throws Throwable
     */
    public function down()
    {
        DB::transaction(function () {
            Permission::whereName(Permission::CAN_INVITE_OTHERS_PERMISSIONS['name'])->delete();
            Role::whereName(Permission::CAN_INVITE_OTHERS_PERMISSIONS['name'])->delete();
        });
    }
}
