<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFacultyIdFieldForMembers extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {

            $table->unsignedInteger('faculty_id')->nullable();
            $table->foreign('faculty_id')
                ->references('id')
                ->on('faculties')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->dropColumn('faculty');

        });
    }

    public function down()
    {
        Schema::table('members', function (Blueprint $table) {

            $table->string('faculty');
            $table->dropForeign('members_faculty_id_foreign');
            $table->dropColumn('faculty_id');

        });
    }
}
