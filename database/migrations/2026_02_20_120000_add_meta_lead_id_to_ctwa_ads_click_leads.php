<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetaLeadIdToCtwaAdsClickLeads extends Migration
{
    public function up()
    {
        Schema::table('ctwa_ads_click_leads', function (Blueprint $table) {
            $table->string('meta_lead_id', 64)->nullable()->unique()->after('id');
        });
    }

    public function down()
    {
        Schema::table('ctwa_ads_click_leads', function (Blueprint $table) {
            $table->dropColumn('meta_lead_id');
        });
    }
}
