-<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTextareaFieldDataTypeColumnToPowerPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
            Schema::table('power_packs', function (Blueprint $table) {
                $table->longText('textarea_field_text')->change();
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
                $table->longText('textarea_field_text')->change();
            });
       
    }
}
