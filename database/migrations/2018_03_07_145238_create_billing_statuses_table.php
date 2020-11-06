<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('alias', 150);
            $table->string('color', 20);
        });

        Artisan::call('db:seed', ['--force' => true, '--class' => 'BillingStatusSeeder']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_statuses');
    }
}
