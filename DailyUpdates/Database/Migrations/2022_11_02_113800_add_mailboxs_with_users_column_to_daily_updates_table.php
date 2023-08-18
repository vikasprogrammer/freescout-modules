<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMailboxsWithUsersColumnToDailyUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_updates', function (Blueprint $table) {
            $table->string('mailboxes',100)->nullable();
            $table->string('users',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_updates', function (Blueprint $table) {
            $table->string('mailboxes',100)->nullable();
            $table->string('users',100)->nullable();
        });
    }
}
