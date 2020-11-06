<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tenant_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_tenant_id');
            $table->unsignedInteger('logo_id')->nullable();
            $table->unsignedInteger('font_id')->nullable();
            $table->unsignedInteger('currency_id')->nullable();
            $table->integer('fee')->nullable();
            $table->string('creator')->nullable();
            $table->string('author')->nullable();
            $table->string('title')->nullable();
            $table->string('subject')->nullable();
            $table->string('keywords')->nullable();
            $table->string('filename')->nullable();
            $table->string('date_format')->nullable();
            $table->json('money_format')->nullable();
            $table->timestamps();

            $table->foreign('currency_id')->references('id')
                ->on('currencies')->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('logo_id')->references('id')
                ->on('images')->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('font_id')->references('id')
                ->on('fields')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('user_tenant_settings');
    }
}
