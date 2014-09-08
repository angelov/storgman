<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'members',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('email');
                $table->string('password', 65);
                $table->string('first_name');
                $table->string('last_name');
                $table->string('faculty');
                $table->string('field_of_study');
                $table->integer('year_of_graduation');
                $table->string('photo')->nullable();
                $table->date('birthday');
                $table->string('facebook')->nullable();
                $table->string('twitter')->nullable();
                $table->string('google_plus')->nullable();
                $table->string('phone')->nullable();
                $table->string('website')->nullable();
                $table->boolean('board_member')->default(false);
                $table->string('position_title')->nullable();
                $table->string('remember_token')->nullable();
                $table->timestamps();

                $table->unique('email');
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
        Schema::drop('members');
    }

}
