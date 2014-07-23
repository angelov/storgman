<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('members', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('email');
            $table->string('password', 65);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('faculty');
            $table->string('field_of_study');
            $table->string('photo')->nullable();
            $table->date('birthday');
            $table->boolean('board_member')->default(false);
            $table->string('position_title');
            $table->string('remember_token')->nullable();
			$table->timestamps();

            $table->unique('email');
		});
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
