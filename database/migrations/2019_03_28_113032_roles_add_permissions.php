<?php

use App\Models\Permission;
use App\Models\Role;
use function foo\func;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RolesAddPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = Permission::whereIn('name', ['read-other-comments', 'manage-other-time-logs'])->pluck('id');
        Role::whereIn('display_name', ['Group Admin', 'Member', 'External'])->get()->each(function (Role $role) use ($permissions) {
            $role->perms()->sync($permissions, false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
