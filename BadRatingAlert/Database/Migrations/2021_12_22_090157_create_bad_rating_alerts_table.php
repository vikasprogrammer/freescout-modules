<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBadRatingAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bad_rating_alerts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mailbox_id')->nullable();
            $table->longText('slack_url')->nullable();
            $table->string('slack_channel_id',255)->nullable();
            $table->string('enable_slack_notification',255)->nullable();
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
        Schema::dropIfExists('bad_rating_alerts');
    }
}
