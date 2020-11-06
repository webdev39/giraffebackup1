<?php

use App\Models\Comment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CommentsAssignToGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Comment::whereNull('groupId')->with('task')->get()->each(function(Comment $comment) {
            $task = $comment->task;
            if($task && $task->board->first()) {
                DB::table('comments')
                    ->where('id', $comment->id)
                    ->update(['groupId' => $task->board->first()->group_id]);
            }
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
