<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialProfilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social_profiles', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('provider');
            $table->string('profile_id');

            $table->unsignedInteger('member_id');
            $table->index('member_id');
            $table->foreign('member_id')
                ->references('id')->on('members')
                ->onDelete('cascade')
                ->onUpdate('cascade');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('social_profiles');
	}

}
