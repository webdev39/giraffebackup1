<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTenantPriorityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tenant_priority', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_tenant_id');
            $table->unsignedInteger('priority_id');

            $table->boolean('is_invisible')->default(false);

            $table->index(['user_tenant_id', 'priority_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_tenant_priority');
    }
}
