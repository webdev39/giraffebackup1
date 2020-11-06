<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToUserTenantSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_tenant_settings', function (Blueprint $table) {
            $table->string('street')->nullable()->after('logo_id');
            $table->string('city')->nullable()->after('logo_id');
            $table->string('postcode')->nullable()->after('logo_id');
            $table->string('web')->nullable()->after('logo_id');
            $table->string('phone')->nullable()->after('logo_id');
            $table->string('email')->nullable()->after('logo_id');
            $table->string('bill_settings')->nullable()->after('logo_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_tenant_settings', function (Blueprint $table) {
            $table->dropColumn('street');
            $table->dropColumn('city');
            $table->dropColumn('postcode');
            $table->dropColumn('web');
            $table->dropColumn('phone');
            $table->dropColumn('email');
            $table->dropColumn('bill_settings');
        });
    }
}
