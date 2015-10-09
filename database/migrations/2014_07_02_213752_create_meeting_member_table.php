<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'meeting_member',
            function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('meeting_id');
                $table->index('meeting_id');
                $table->foreign('meeting_id')
                    ->references('id')
                    ->on('meetings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->unsignedInteger('member_id');
                $table->index('member_id');
                $table->foreign('member_id')
                    ->references('id')
                    ->on('members')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('meeting_member');
    }
}
