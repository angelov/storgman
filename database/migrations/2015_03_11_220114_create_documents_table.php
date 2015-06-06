<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(
            'documents',
            function(Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('url');
                $table->unsignedInteger('submitted_by');
                $table->index('submitted_by');
                $table->foreign('submitted_by')
                    ->references('id')
                    ->on('members')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->boolean('board_only')->default(false);
                $table->timestamps();
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
		Schema::drop('documents');
	}

}
