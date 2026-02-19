<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByToFlowGroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('flow_grounds') && ! Schema::hasColumn('flow_grounds', 'created_by')) {
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable();
            });
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
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
        if (Schema::hasTable('flow_grounds') && Schema::hasColumn('flow_grounds', 'created_by')) {
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
            });
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->dropColumn('created_by');
            });
        }
    }
}
