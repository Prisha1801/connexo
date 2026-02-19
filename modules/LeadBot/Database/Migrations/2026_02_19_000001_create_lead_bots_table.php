<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_bots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('app_id');
            $table->string('trigger_event');
            $table->string('webhook_token', 80)->unique();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_bots');
    }
};

