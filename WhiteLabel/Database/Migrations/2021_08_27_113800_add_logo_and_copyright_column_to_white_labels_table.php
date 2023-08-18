-<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLogoAndCopyrightColumnToWhiteLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('white_labels', function (Blueprint $table) {
            $table->longText('logo')->nullable();
            $table->longText('favicon')->nullable();
            $table->longText('copyrightText')->nullable();
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
            $table->longText('logo')->nullable();
            $table->longText('favicon')->nullable();
            $table->longText('copyrightText')->nullable();
        });
    }
}
