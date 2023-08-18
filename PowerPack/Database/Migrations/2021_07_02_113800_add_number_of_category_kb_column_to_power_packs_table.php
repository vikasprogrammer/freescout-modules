-<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class addNumberOfCategoryKbColumnToPowerPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('power_packs', function (Blueprint $table) {
            $table->string('number_of_category_kb',100)->nullable();
            $table->string('number_of_article_kb',100)->nullable();
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
            $table->string('number_of_category_kb',100)->nullable();
            $table->string('number_of_article_kb',100)->nullable();
        });

    }
}
