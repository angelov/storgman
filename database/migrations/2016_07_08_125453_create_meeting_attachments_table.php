<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingAttachmentsTable extends Migration
{
    public function up()
    {
        Schema::create('meeting_attachments', function (Blueprint $table) {
            $table->increments('id');

            $table->string('filename');
            $table->string('storage_filename');

            $table->float('size')->comment('kilobytes');

            $table->unsignedInteger('meeting_id')->nullable();
            $table->foreign('meeting_id')
                ->references('id')
                ->on('meetings')
                ->onDelete('set null') // so we can later see that the attachment is not used and delete the file
                ->onUpdate('cascade');

            $table->unsignedInteger('owner_id')->nullable();
            $table->foreign('owner_id')
                ->references('id')
                ->on('members')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('meeting_attachments');
    }
}
