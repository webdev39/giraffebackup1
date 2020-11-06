<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

class AddTrackingPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ([Permission::READ_OTHER_COMMENTS_PERMISSION, Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION] as $data) {
            if(!Permission::where('name', $data['name'])->count()) {
                (new Permission())
                    ->forceFill($data)
                    ->save();
            }
        }
    }

    /**
     * @throws Exception
     */
    public function down()
    {
        foreach ([Permission::READ_OTHER_COMMENTS_PERMISSION, Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION] as $data) {
            Permission::where('name', $data['name'])->delete();
        }
    }
}
