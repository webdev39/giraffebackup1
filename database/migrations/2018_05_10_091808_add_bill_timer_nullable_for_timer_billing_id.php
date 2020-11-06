<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillTimerNullableForTimerBillingId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_timers', function (Blueprint $table) {
            $table->unsignedInteger('timer_billing_id')->nullable()->change();
            $table->unsignedInteger('user_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_timers', function (Blueprint $table) {
            $table->unsignedInteger('timer_billing_id')->change();
            $table->removeColumn('user_id');
        });
    }
}
