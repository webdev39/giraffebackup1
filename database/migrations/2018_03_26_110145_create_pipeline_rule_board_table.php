<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePipelineRuleBoardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pipeline_rule_board', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pipeline_rule_id');
            $table->unsignedInteger('board_id');
            $table->timestamps();

            $table->foreign('pipeline_rule_id')->references('id')->on('pipeline_rules')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('board_id')->references('id')->on('boards')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pipeline_rule_board');
    }
}
