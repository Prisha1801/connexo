<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionToFlowGroundsIfMissing extends Migration
{
    public function up()
    {
        if (Schema::hasTable('flow_grounds') && ! Schema::hasColumn('flow_grounds', 'description')) {
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('flow_grounds') && Schema::hasColumn('flow_grounds', 'description')) {
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
}
