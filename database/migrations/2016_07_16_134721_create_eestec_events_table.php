<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEestecEventsTable extends Migration
{
    public function up()
    {
        Schema::create('eestec_events', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title');

            $table->text('description')->nullable();

            $table->unsignedInteger('host_id');
            $table->foreign('host_id')
                ->references('id')
                ->on('local_committees')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->date('start_date');
            $table->date('end_date');
            $table->dateTime('deadline');

            $table->string('image');

            $table->string('url');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('eestec_events');
    }
}
