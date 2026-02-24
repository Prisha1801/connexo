<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_address', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id', 255)->nullable();
            $table->string('user_name', 255)->nullable();
            $table->string('phone_number', 255)->nullable();
            $table->string('house_number', 255)->nullable();
            $table->text('landmark_area')->nullable();
            $table->string('pin_code', 255)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('building_name', 255)->nullable();
            $table->string('state', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('status', 255)->default('0');
            $table->string('tower_number', 255)->nullable();
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
        Schema::dropIfExists('order_address');
    }
}
