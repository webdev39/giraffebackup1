<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentCommentTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachment_comment', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('attachment_id');
            $table->unsignedInteger('comment_id');

            $table->json('spatial')->nullable();

            $table->index(['attachment_id', 'comment_id']);

            $table->foreign('attachment_id')->references('id')->on('attachments')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('comments')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachment_comment');
    }
}
