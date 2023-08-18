<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusyTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('busy_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject',255)->nullable();
            $table->integer('mailbox')->nullable();
            $table->integer('conversation_id')->nullable();
            $table->string('users',255)->nullable();
            $table->string('counts',255)->nullable();
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
        Schema::dropIfExists('busy_tickets');
    }
}
