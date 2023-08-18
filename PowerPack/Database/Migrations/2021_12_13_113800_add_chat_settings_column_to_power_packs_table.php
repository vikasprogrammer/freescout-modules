-<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChatSettingsColumnToPowerPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('power_packs', function (Blueprint $table) {
            $table->string('minutes',100)->nullable();
            $table->longText('chat_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('power_packs', function (Blueprint $table) {
            $table->string('minutes',100)->nullable();
            $table->longText('chat_message')->nullable();
        });
    }
}
