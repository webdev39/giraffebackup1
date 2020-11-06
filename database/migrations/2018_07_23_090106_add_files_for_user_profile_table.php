<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFilesForUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
            $table->dropColumn('image_id');

            $table->unsignedInteger('avatar_id')->nullable();
            $table->unsignedInteger('background_id')->nullable();

            $table->foreign('avatar_id')->references('id')->on('images');
            $table->foreign('background_id')->references('id')->on('images');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->removeColumn('avatar_id');
            $table->removeColumn('background_id');
        });
    }
}
