-<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRatingOptionColumnToBadRatingAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bad_rating_alerts', function (Blueprint $table) {
            $table->string('rating_great',200)->nullable();
            $table->string('rating_okay',200)->nullable();
            $table->string('rating_not_okay',200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bad_rating_alerts', function (Blueprint $table) {
            $table->string('rating_great',200)->nullable();
            $table->string('rating_okay',200)->nullable();
            $table->string('rating_not_okay',200)->nullable();
        });
    }
}
