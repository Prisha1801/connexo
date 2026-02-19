<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlowGroundSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flow_ground_submissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('flow_ground_id')
                ->constrained('flow_grounds')
                ->cascadeOnDelete();

            $table->string('phone');                 // WhatsApp number
            $table->json('payload');                 // Raw webhook payload
            $table->json('response_json');           // Clean form data

            $table->enum('status', ['pending', 'completed', 'failed'])
                ->default('pending');

            $table->string('error_message')->nullable();

            // Meta audit fields
            $table->string('meta_flow_id')->nullable();
            $table->string('meta_flow_version')->nullable();
            $table->string('meta_flow_revision')->nullable();
            $table->string('meta_flow_status')->nullable();
            $table->string('meta_flow_error_message')->nullable();
            $table->string('meta_flow_error_code')->nullable();
            $table->json('meta_flow_error_details')->nullable();
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
        Schema::dropIfExists('flow_ground_submissions');
    }
}
