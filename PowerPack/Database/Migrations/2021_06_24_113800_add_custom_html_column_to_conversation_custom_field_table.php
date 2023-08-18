-<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomHtmlColumnToConversationCustomFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
       
            Schema::table('conversation_custom_field', function (Blueprint $table) {
                $table->text('custom_html')->nullable();
            });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    
            Schema::table('conversation_custom_field', function (Blueprint $table) {
                $table->text('custom_html')->nullable();
            });
 
    }
}
