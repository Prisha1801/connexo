<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class ExpandFlowGroundsStatusEnum extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('flow_grounds') || ! Schema::hasColumn('flow_grounds', 'status')) {
            return;
        }

        // The original create table migration has enum('draft','published').
        // Later AddStatusToFlowGroundsTable only runs when the column is missing.
        // This migration ensures 'deprecated' is supported in existing databases.
        try {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE `flow_grounds` MODIFY `status` ENUM('draft','published','deprecated') NOT NULL DEFAULT 'draft'");
            }
        } catch (\Throwable $e) {
            // Best-effort migration; if it fails, app will still run but 'deprecated' updates may fail at DB level.
        }
    }

    public function down()
    {
        if (! Schema::hasTable('flow_grounds') || ! Schema::hasColumn('flow_grounds', 'status')) {
            return;
        }

        try {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE `flow_grounds` MODIFY `status` ENUM('draft','published') NOT NULL DEFAULT 'draft'");
            }
        } catch (\Throwable $e) {
        }
    }
}

