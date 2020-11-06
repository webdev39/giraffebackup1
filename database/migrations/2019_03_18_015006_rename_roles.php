<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

class RenameRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::whereName('report-own-data')
            ->update(['display_name' => 'report-own-data']);
        Role::whereName('report-other-data')
            ->update(['display_name' => 'report-other-data']);
        Role::whereName('time-tracking')
            ->update(['display_name' => 'time-tracking-access']);
        Role::whereName('read-billing')
            ->update(['display_name' => 'billing-module']);
        Role::whereName('manage-customers')
            ->update(['display_name' => 'manage-customers']);
        Role::whereName('create-group')
            ->update(['display_name' => 'create-group']);
        Role::whereName('manage-group-level-role')
            ->update(['display_name' => 'manage-group-level-role']);
        Role::whereName('acp-access')
            ->update(['display_name' => 'manage-users']);
        Role::whereName('create-tenant-invite')
            ->update(['display_name' => 'read+join-group']);
        Role::whereName('read-group')
            ->update(['is_one_permission' => 0]);
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
