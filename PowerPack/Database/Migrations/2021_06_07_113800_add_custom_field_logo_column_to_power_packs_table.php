-<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class addCustomFieldLogoColumnToPowerPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
           Schema::table('power_packs', function (Blueprint $table) {
            $table->longText('eupLogoImage')->nullable();
            $table->string('eupLogoText',150)->nullable();
            $table->longText('kbLogoImage')->nullable();
            $table->string('kbLogoText',150)->nullable();
            $table->string('enable_text_logo',150)->nullable();
            $table->string('kb_enable_text_logo',150)->nullable();
            
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
            $table->longText('eupLogoImage')->nullable();
            $table->string('eupLogoText',150)->nullable();
            $table->longText('kbLogoImage')->nullable();
            $table->string('kbLogoText',150)->nullable();
            $table->string('enable_text_logo',150)->nullable();
            $table->string('kb_enable_text_logo',150)->nullable();
            
        });

    }
}
