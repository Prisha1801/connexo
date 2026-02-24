<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingCatalogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_catalogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('body')->nullable();
            $table->longText('footer')->nullable();
            $table->string('payment_configuration', 255)->nullable();
            $table->string('payment_configuration_other', 255)->nullable();
            $table->string('shipping', 255)->nullable();
            $table->text('shipping_description')->nullable();
            $table->tinyInteger('payment_type')->default(0);
            $table->tinyInteger('address_message_enable')->default(0);
            $table->tinyInteger('payment_method_enable')->default(0);
            $table->longText('address_mess')->nullable();
            $table->string('whatsapp_number', 255)->nullable();
            $table->longText('order_message');
            $table->integer('company_id');
            $table->string('product_id', 255)->nullable();
            $table->timestamps(0); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_catalogs');
    }
}
