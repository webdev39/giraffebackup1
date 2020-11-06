<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filters', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_tenant_id');
            $table->string('name');
            $table->string('range')->nullable();
            $table->json('assigner_ids')->nullable();
            $table->json('group_ids')->nullable();
            $table->json('board_ids')->nullable();
            $table->json('priority_ids')->nullable();
            $table->boolean('status')->nullable();
            $table->timestamps();

            $table->foreign('user_tenant_id')->references('id')->on('user_tenant')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('filters');
    }
}
