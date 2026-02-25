<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCatalogTriggerKeywordsToSettingCatalogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_catalogs', function (Blueprint $table) {
            if (!Schema::hasColumn('setting_catalogs', 'catalog_trigger_keywords')) {
                $table->string('catalog_trigger_keywords')->nullable()->after('catalog_tagline');
            }
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
            if (Schema::hasColumn('setting_catalogs', 'catalog_trigger_keywords')) {
                $table->dropColumn('catalog_trigger_keywords');
            }
        });
    }
}

