<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePowerPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('power_packs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mailbox_id')->nullable();
            $table->string('contact_window_css',255)->nullable();
            $table->string('nav_bg_for_kb_portal',255)->nullable();
            $table->string('active_menu_item_bg_kb_portal',255)->nullable();
            $table->string('nav_bg_for_user_end_portal',255)->nullable();
            $table->string('active_menu_item_bg_user_end_portal',255)->nullable();
            $table->longText('add_css_kb_portal')->nullable();
            $table->longText('add_css_user_end_portal')->nullable();
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
        Schema::dropIfExists('power_packs');
    }
}
