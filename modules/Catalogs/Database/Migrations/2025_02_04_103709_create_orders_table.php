<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_name', 225)->nullable();
            $table->integer('company_id')->nullable();
            $table->string('reference_id', 255)->nullable();
            $table->string('phone_number', 225)->nullable();
            $table->string('subtotal_value', 225)->nullable();
            $table->string('subtotal_offset', 255)->nullable();
            $table->longText('message_id')->nullable();
            $table->string('tax_value', 255)->nullable();
            $table->string('house_number', 255)->nullable();
            $table->string('landmark_area', 225)->nullable();
            $table->string('pin_code', 225)->nullable();
            $table->string('city', 225)->nullable();
            $table->string('building_name', 255)->nullable();
            $table->string('state', 255)->nullable();
            $table->longText('address')->nullable();
            $table->string('tower_number', 255)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->string('transaction_type', 225)->nullable();
            $table->string('shipping_cast', 255)->nullable();
            $table->string('currency', 225)->nullable();
            $table->string('payment_status', 225)->nullable();
            $table->string('tax_offset', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->string('entry_id', 255)->nullable();
            $table->string('type', 255)->nullable();
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
        Schema::dropIfExists('orders');
    }
}
