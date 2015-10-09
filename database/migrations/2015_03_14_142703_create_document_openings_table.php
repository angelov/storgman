<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentOpeningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_openings', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('document_id');
            $table->index('document_id');
            $table->foreign('document_id')
                ->references('id')
                ->on('documents')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedInteger('member_id');
            $table->index('member_id');
            $table->foreign('member_id')
                ->references('id')
                ->on('members')
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
        Schema::drop('document_openings');
    }
}
