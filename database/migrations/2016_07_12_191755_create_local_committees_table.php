<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalCommitteesTable extends Migration
{
    public function up()
    {
        Schema::create('local_committees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');

            $table->unsignedInteger('city_id');
            $table->foreign('city_id')
                ->references('id')
                ->on('cities')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('local_committees');
    }
}
