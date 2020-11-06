<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimerTaskLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timer_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('timer_id');
            $table->unsignedInteger('log_id');
            $table->timestamps();

            $table->foreign('log_id')->references('id')->on('logs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('timer_id')->references('id')->on('timers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timer_log');
    }
}
