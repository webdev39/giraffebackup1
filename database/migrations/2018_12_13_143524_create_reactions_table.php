<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("targetable_id");
            $table->string("targetable_type");
            $table->unsignedInteger("sourceable_id")->nullable();
            $table->string("sourceable_type")->nullable();
            $table->integer('user_tenant_id')->unsigned()->index();
            $table->string('reaction');
            $table->timestamps();

            $table->index(["targetable_id", "targetable_type"]);
            $table->index(["sourceable_id", "sourceable_type"]);

            $table->unique(['targetable_type','targetable_id', 'user_tenant_id', 'reaction'], 'targetable_user_tenant_reaction_unique');

            $table->foreign('user_tenant_id')->references('id')->on('user_tenant')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reactions');
    }
}
