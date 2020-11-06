<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ActivityLogCreateGroupIdBoardIdTaskId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->unsignedInteger('group_id')->nullable();
            $table->unsignedInteger('board_id')->nullable();
            $table->unsignedInteger('task_id')->nullable();
            $table->string('action')->nullable();
            $table->string('field')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropColumn('group_id');
            $table->dropColumn('board_id');
            $table->dropColumn('task_id');
            $table->dropColumn('action');
            $table->dropColumn('field');
        });
    }
}
