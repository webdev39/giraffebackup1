<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyncRolePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', ['--force' => true, '--class' => 'UpdatePermissionsSeeder']);
        Artisan::call('db:seed', ['--force' => true, '--class' => 'PermissionSyncSeeder']);
        Artisan::call('db:seed', ['--force' => true, '--class' => 'PermissionsSeeder']);
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
