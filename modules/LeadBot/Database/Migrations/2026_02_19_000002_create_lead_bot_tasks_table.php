<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_bot_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('lead_bot_id')->constrained('lead_bots')->cascadeOnDelete();
            $table->string('task_type');
            $table->string('task_name')->nullable();
            $table->unsignedInteger('task_order')->default(0);
            $table->json('task_config')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_bot_tasks');
    }
};

