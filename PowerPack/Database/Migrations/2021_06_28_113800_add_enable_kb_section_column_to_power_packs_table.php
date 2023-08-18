-<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEnableKbSectionColumnToPowerPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
       
            Schema::table('power_packs', function (Blueprint $table) {
                $table->string('enable_kb_section',150)->nullable();
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
                $table->string('enable_kb_section',150)->nullable();
            });
  
        
    }
}
