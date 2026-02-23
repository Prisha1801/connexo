<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workflow_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('workflow_tasks', 'order')) {
                $table->unsignedInteger('order')->default(0)->after('task_config');
            }
        });
    }

    public function down(): void
    {
        Schema::table('workflow_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('workflow_tasks', 'order')) {
                $table->dropColumn('order');
            }
        });
    }
};
