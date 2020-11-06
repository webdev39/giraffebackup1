<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTenantTemplatesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tenant_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_tenant_id');
            $table->string('type');
            $table->string('key');
            $table->text('content')->nullable();
            $table->timestamps();

            $table->foreign('user_tenant_id')->references('id')
                ->on('user_tenant')->onUpdate('cascade')->onDelete('cascade');
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
