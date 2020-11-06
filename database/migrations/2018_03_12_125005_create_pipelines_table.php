<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreatePipelinesTable.
 */
class CreatePipelinesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pipelines', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tenant_id');
            $table->boolean('is_active')->default(0);
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('host');
            $table->integer('port');
            $table->string('encryption', 4);
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('pipelines');
	}
}
