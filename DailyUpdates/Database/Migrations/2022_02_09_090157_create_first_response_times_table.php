<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirstResponseTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('first_response_times', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mailbox_id')->nullable();
            $table->integer('conversation_id')->nullable();
            $table->string('first',100)->nullable();
            $table->string('times',255)->nullable();
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
        Schema::dropIfExists('first_response_times');
    }
}
