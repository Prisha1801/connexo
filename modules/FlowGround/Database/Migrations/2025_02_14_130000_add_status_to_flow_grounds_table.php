<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToFlowGroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('flow_grounds') && ! Schema::hasColumn('flow_grounds', 'status')) {
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->enum('status', ['draft', 'published','deprecated'])->default('draft');
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
        if (Schema::hasTable('flow_grounds') && Schema::hasColumn('flow_grounds', 'status')) {
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
}
