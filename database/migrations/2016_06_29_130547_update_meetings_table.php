<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('title');

            $table->unsignedInteger('report_author')->nullable();
            $table->index('report_author');
            $table->foreign('report_author')
                ->references('id')
                ->on('members')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->text('minutes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropForeign('meetings_report_author_foreign');
            $table->dropColumn('report_author');
            $table->dropColumn('minutes');
        });
    }
}
