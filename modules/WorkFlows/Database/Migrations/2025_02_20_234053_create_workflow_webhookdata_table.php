<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkflowWebhookdataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow_webhook_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('workflow_id')->constrained()->onDelete('cascade');
            $table->json('payload')->nullable();
            $table->json('mapped_data')->nullable();
            $table->text('response')->nullable();
            $table->boolean('success')->default(false);
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workflow_webhook_data');
    }
}
