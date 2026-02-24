<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnSelfpickupDeliveryInstoreToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_catalogs', function (Blueprint $table) {
            $table->boolean('enable_self_pickup')->default(1);
            $table->boolean('enable_in_store')->default(1);
            $table->boolean('enable_delivery')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting_catalogs', function (Blueprint $table) {
            $table->dropColumn(['enable_self_pickup', 'enable_in_store', 'enable_delivery']);
        });
    }
}
