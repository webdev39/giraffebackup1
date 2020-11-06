<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimerBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timer_billings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('timer_id');
            $table->unsignedInteger('billing_status_id');
            $table->integer('rate')
                ->default(\App\Models\TimerBilling::DEFAULT_RATE);
            $table->string('time_bill');
            $table->timestamps();

            $table->foreign('billing_status_id')
                ->references('id')
                ->on('billing_statuses')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('timer_id')
                ->references('id')
                ->on('timers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timer_billings');
    }
}
