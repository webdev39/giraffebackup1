<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserTenantTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tenant_task', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_tenant_id')->nullable();
            $table->unsignedInteger('task_id')->nullable();
            $table->timestamps();

            $table->foreign('user_tenant_id')->references('id')->on('user_tenant')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('tasks')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_tenant_task');
    }
}
