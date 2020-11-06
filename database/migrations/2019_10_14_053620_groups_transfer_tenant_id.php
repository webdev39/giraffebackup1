<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GroupsTransferTenantId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::transaction(function() {
            Tenant::get()->each(function(Tenant $tenant) {
                $owner = $tenant->owner();

                if (isset($owner->user_tenant)) {
                    $groups = $owner->user_tenant->groups;
                    foreach ($groups as $group) {
                        $group->tenant_id = $tenant->id;
                        $group->save();
                    }
                }

            });
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
