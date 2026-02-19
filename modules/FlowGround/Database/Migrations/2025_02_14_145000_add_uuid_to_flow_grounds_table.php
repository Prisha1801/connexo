<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddUuidToFlowGroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('flow_grounds') && ! Schema::hasColumn('flow_grounds', 'uuid')) {
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique();
            });

            // Backfill existing rows with UUIDs
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('UPDATE flow_grounds SET uuid = UUID() WHERE uuid IS NULL');
            } else {
                foreach (DB::table('flow_grounds')->whereNull('uuid')->get() as $row) {
                    DB::table('flow_grounds')->where('id', $row->id)->update([
                        'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('flow_grounds') && Schema::hasColumn('flow_grounds', 'uuid')) {
            Schema::table('flow_grounds', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
}
