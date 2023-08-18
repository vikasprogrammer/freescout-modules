<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnvatoCustomFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('envato_custom_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mailbox_id');
            $table->string('name', 75);
            $table->string('custom_field_name', 150);
            $table->text('options')->nullable();
            $table->boolean('required')->default(false);
            $table->integer('sort_order')->default(1);
            $table->timestamps();

            $table->index(['mailbox_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('envato_custom_fields');
    }
}
