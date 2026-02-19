<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoriesToFlowGroundsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('flow_grounds') && ! Schema::hasColumn('flow_grounds', 'categories')) {
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->json('categories')->nullable()->after('description');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('flow_grounds') && Schema::hasColumn('flow_grounds', 'categories')) {
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->dropColumn('categories');
            });
        }
    }
}

