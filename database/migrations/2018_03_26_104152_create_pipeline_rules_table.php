<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePipelineRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pipeline_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pipeline_id');
            $table->unsignedInteger('pipeline_filter_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('keywords');
            $table->timestamps();

            $table->foreign('pipeline_id')->references('id')->on('pipelines')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('pipeline_filter_id')->references('id')->on('pipeline_filters')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pipeline_rules');
    }
}
