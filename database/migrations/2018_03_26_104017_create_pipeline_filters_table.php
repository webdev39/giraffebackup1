<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePipelineFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pipeline_filters', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_active');
            $table->string('name');
            $table->string('display_name');
            $table->timestamps();
        });
        Artisan::call('db:seed', ['--force' => true, '--class' => 'PipelineFiltersSeeder']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pipeline_filters');
    }
}
