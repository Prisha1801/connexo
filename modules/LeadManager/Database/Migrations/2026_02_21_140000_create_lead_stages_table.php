<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lead_stages')) {
            return;
        }

        Schema::create('lead_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        // Seed default stages for existing companies (only if none exist for that company)
        if (Schema::hasTable('companies')) {
            $companies = \DB::table('companies')->pluck('id');
            $defaults = [
                ['name' => 'New', 'sort_order' => 10],
                ['name' => 'Contacted', 'sort_order' => 20],
                ['name' => 'Interested', 'sort_order' => 30],
                ['name' => 'Qualified', 'sort_order' => 40],
                ['name' => 'Won', 'sort_order' => 50],
                ['name' => 'Lost', 'sort_order' => 60],
            ];
            foreach ($companies as $companyId) {
                $exists = \DB::table('lead_stages')->where('company_id', $companyId)->exists();
                if ($exists) continue;
                foreach ($defaults as $d) {
                    \DB::table('lead_stages')->insert([
                        'company_id' => $companyId,
                        'name' => $d['name'],
                        'sort_order' => $d['sort_order'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_stages');
    }
};
