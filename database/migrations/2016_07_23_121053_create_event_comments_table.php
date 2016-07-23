<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('event_comments', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('author_id');
            $table->foreign('author_id')
                ->references('id')
                ->on('members')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedInteger('event_id');
            $table->foreign('event_id')
                ->references('id')
                ->on('eestec_events')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->text('content');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('event_comments');
    }
}
