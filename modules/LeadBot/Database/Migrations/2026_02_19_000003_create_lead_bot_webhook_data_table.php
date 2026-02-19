<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_bot_webhook_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('lead_bot_id')->constrained('lead_bots')->cascadeOnDelete();
            $table->json('payload')->nullable();
            $table->json('headers')->nullable();
            $table->json('mapped_data')->nullable();
            $table->json('task_results')->nullable();
            $table->longText('response')->nullable();
            $table->boolean('success')->default(false);
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_bot_webhook_data');
    }
};

