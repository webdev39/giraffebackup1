<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillLayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_layouts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('path');
            $table->string('bill_date');
            $table->unsignedInteger('bill_layout_type_id');
            $table->unsignedInteger('bill_id');
            $table->timestamps();

            $table->foreign('bill_layout_type_id')->references('id')->on('bill_layout_types')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('bill_id')->references('id')->on('bills')
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
        Schema::dropIfExists('bill_layouts');
    }
}
