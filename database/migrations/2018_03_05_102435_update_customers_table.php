<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedInteger('tenant_id')->default(1);
            $table->string('city');
            $table->string('country');
            $table->string('email');
            $table->string('telephone');
            $table->string('street');
            $table->string('postcode')->nullable();
            $table->string('house');
            $table->decimal('hourly_rate', 15, 2);

            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
            $table->dropColumn('city');
            $table->dropColumn('country');
            $table->dropColumn('email');
            $table->dropColumn('telephone');
            $table->dropColumn('street');
            $table->dropColumn('tenant_id');
            $table->dropColumn('house');
            $table->dropColumn('hourly_rate');
        });
    }
}
