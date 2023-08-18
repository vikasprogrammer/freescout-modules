-<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomFieldsColorColumnToPowerPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
            Schema::table('power_packs', function (Blueprint $table) {
                $table->string('end_btn_bg_color',255)->nullable();
                $table->string('end_text_color',255)->nullable();
                $table->string('bk_btn_bg_color',255)->nullable();
                $table->string('kb_text_color',255)->nullable();
                $table->string('custom_html',150)->nullable();
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
                $table->string('end_btn_bg_color',255)->nullable();
                $table->string('end_text_color',255)->nullable();
                $table->string('bk_btn_bg_color',255)->nullable();
                $table->string('kb_text_color',255)->nullable();
                $table->string('custom_html',150)->nullable();
            });
    
    }
}
