<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('catalog_id', 225);
            $table->integer('company_id');
            $table->string('product_id', 225);
            $table->string('name', 11);
            $table->string('product_name', 255)->nullable();
            $table->longText('description')->nullable();
            $table->string('price', 255)->nullable();
            $table->longText('image_url')->nullable();
            $table->string('created_time', 255)->nullable();
            $table->string('retailer_id', 255);
            $table->timestamp('deleted_at')->nullable()->default(null);
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
        Schema::dropIfExists('catalog_products');
    }
}
