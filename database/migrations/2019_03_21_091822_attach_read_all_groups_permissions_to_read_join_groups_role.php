<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AttachReadAllGroupsPermissionsToReadJoinGroupsRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!$permission = Permission::whereName('read-all-groups')->first()) {
            $permission = (new Permission())->forceFill([
                'name'         => 'read-all-groups',
                'display_name' => 'Read All Groups',
                'description'  => 'User can read Groups'
            ]);
            $permission->save();
        }

        $role = Role::where('display_name', 'read+join-group')->first();
        $perms = $role->perms->pluck('id');
        $perms = $perms->push($permission->id);
        $role->perms()->sync($perms);
    }

    /**
     * @throws Exception
     */
    public function down()
    {
        $permission = Permission::whereName('read-all-groups')->first();
        $role = Role::where('display_name', 'read+join-group')->first();
        $perms = $role->perms->pluck('id');
        $perms = $perms->filter(function($id) use($permission) {
            return $id != $permission->id;
        });
        $role->perms()->sync($perms);
        $permission->delete();
    }
}
