<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'source')) {
                $table->string('source')->default('manual')->after('contact_id');
            }
            if (!Schema::hasColumn('leads', 'qualified')) {
                $table->boolean('qualified')->nullable()->after('stage');
            }
            if (!Schema::hasColumn('leads', 'lost_reason')) {
                $table->string('lost_reason')->nullable()->after('qualified');
            }
            if (!Schema::hasColumn('leads', 'notes')) {
                $table->text('notes')->nullable()->after('lost_reason');
            }
            if (!Schema::hasColumn('leads', 'first_contacted_at')) {
                $table->timestamp('first_contacted_at')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $columns = ['source', 'qualified', 'lost_reason', 'notes', 'first_contacted_at'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('leads', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
