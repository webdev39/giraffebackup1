<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

class CreateOnePermissionRoles extends Migration
{
    /**
     *
     */
    public function up()
    {
        foreach (Permission::get() as $permission) {
            /** @var $role Role */
            $role = Role::create([
                'name' => $permission->name,
                'display_name' => $permission->display_name,
                'description' => $permission->description,
                'is_default' => 0,
                'is_manual' => 0,
                'is_one_permission' => 1,
            ]);
            $role->perms()->sync($permission->id);
        }
    }
}
