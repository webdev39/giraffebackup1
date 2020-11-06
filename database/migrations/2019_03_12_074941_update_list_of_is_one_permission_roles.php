<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

class UpdateListOfIsOnePermissionRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::query()->update(['is_one_permission' => 0]);

        Role::whereIn('name', [
            'report-own-data',
            'report-other-data',
            'time-tracking',
            'read-billing',
            'create-group',
            'read-group',
            'acp-access',
            'manage-group-level-role',
            'manage-customers',
            'create-tenant-invite',
        ])
        ->update([
            'is_one_permission' => 1,
        ]);
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
