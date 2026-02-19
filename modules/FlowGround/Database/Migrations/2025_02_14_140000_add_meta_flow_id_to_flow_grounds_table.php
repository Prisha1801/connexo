<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetaFlowIdToFlowGroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('flow_grounds') && ! Schema::hasColumn('flow_grounds', 'meta_flow_id')) {
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->string('meta_flow_id')->nullable();
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
        if (Schema::hasTable('flow_grounds') && Schema::hasColumn('flow_grounds', 'meta_flow_id')) {
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->dropColumn('meta_flow_id');
            });
        }
    }
}
