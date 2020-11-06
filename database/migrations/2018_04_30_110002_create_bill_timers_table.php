<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_timers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('time');
            $table->string('comment')->nullable();
            $table->float('rate');
            $table->unsignedInteger('bill_id');
            $table->unsignedInteger('timer_billing_id');
            $table->timestamps();

            $table->foreign('bill_id')->references('id')->on('bills')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('timer_billing_id')->references('id')->on('timer_billings')
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
        Schema::dropIfExists('bill_timers');
    }
}
