
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ColumnToCreateEnvatoCustomFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('envato_custom_fields', function (Blueprint $table) {
            $table->integer('custom_fields_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('envato_custom_fields', function (Blueprint $table) {
            $table->integer('custom_fields_id')->nullable();
        });
    }
}
