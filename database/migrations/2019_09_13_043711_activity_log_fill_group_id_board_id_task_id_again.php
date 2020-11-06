<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Illuminate\Support\Facades\DB;

class ActivityLogFillGroupIdBoardIdTaskIdAgain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('activity_log')->get()->each(function($row) {
            $properties = json_decode($row->properties);
            DB::table('activity_log')
                ->where('id', $row->id)
                ->update([
                    'group_id' => @$properties->group_id,
                    'board_id' => @$properties->board_id,
                    'task_id' => @$properties->task_id,
                    'action' => @$properties->action,
                    'field' => @$properties->field,
                ]);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
