<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_attachment', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('log_id');
            $table->unsignedInteger('attachment_id');
            $table->timestamps();

            $table->foreign('log_id')->references('id')->on('logs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('attachment_id')->references('id')->on('attachments')->onUpdate('cascade')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_attachment');
    }
}
