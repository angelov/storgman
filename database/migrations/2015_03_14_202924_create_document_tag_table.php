<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_tag', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('document_id');
            $table->index('document_id');
            $table->foreign('document_id')
                ->references('id')
                ->on('documents')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedInteger('tag_id');
            $table->index('tag_id');
            $table->foreign('tag_id')
                ->references('id')
                ->on('tags')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('document_tag');
    }
}
