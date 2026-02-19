<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlowGroundVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flow_ground_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flow_ground_id')->constrained('flow_grounds')->onDelete('cascade');
            $table->unsignedInteger('version')->default(1);
            $table->json('flow_json');
            $table->boolean('synced_to_meta')->default(false);
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
        Schema::dropIfExists('flow_ground_versions');
    }
}
