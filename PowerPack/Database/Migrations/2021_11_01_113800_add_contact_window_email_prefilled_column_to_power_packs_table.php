-<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContactWindowEmailPrefilledColumnToPowerPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        if (!Schema::hasColumn('power_packs', 'contact_window_email_prefilled_kb')){
            Schema::table('power_packs', function (Blueprint $table) {
                $table->string('contact_window_email_prefilled_kb')->nullable();
            });   
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('power_packs', 'contact_window_email_prefilled_kb')){
            Schema::table('power_packs', function (Blueprint $table)
            {
                $table->dropColumn('contact_window_email_prefilled_kb');
            });
        }
    }
}
