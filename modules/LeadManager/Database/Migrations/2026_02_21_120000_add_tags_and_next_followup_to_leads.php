<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'tags')) {
                $table->string('tags')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('leads', 'location')) {
                $table->string('location')->nullable()->after('tags');
            }
            if (!Schema::hasColumn('leads', 'next_follow_up_at')) {
                $table->timestamp('next_follow_up_at')->nullable()->after('first_contacted_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'tags')) $table->dropColumn('tags');
            if (Schema::hasColumn('leads', 'location')) $table->dropColumn('location');
            if (Schema::hasColumn('leads', 'next_follow_up_at')) $table->dropColumn('next_follow_up_at');
        });
    }
};
