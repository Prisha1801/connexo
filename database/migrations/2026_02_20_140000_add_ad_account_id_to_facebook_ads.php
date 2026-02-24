<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facebook_ads', function (Blueprint $table) {
            if (!Schema::hasColumn('facebook_ads', 'ad_account_id')) {
                $table->string('ad_account_id', 64)->nullable()->after('ad_account');
            }
        });
    }

    public function down(): void
    {
        Schema::table('facebook_ads', function (Blueprint $table) {
            $table->dropColumn('ad_account_id');
        });
    }
};
