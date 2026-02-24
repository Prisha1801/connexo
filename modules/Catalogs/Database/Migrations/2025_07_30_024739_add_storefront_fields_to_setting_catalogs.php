<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStorefrontFieldsToSettingCatalogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_catalogs', function (Blueprint $table) {
            // StoreFront fields

            $table->string('shipping_free_from_amount')->nullable()->after('shipping_description');
            $table->tinyInteger('isShippingFree')->default(0)->after('shipping_free_from_amount');

            $table->string('logo')->nullable()->after('product_id');
            $table->string('business_name')->nullable()->after('logo');
            $table->text('business_address')->nullable()->after('business_name');
            $table->string('business_phone')->nullable()->after('business_address');
            $table->string('business_whatsapp')->nullable()->after('business_phone');
            $table->string('business_email')->nullable()->after('business_whatsapp');
            $table->string('gstin_vat')->nullable()->after('business_email');
            $table->string('currency_code')->default('INR')->after('gstin_vat');
            $table->string('currency_symbol')->default('₹')->after('currency_code');
            $table
                ->enum('discount_type', ['percentage', 'fixed'])
                ->default('percentage')
                ->after('currency_symbol');
            $table->decimal('default_discount', 8, 2)->default(0)->after('discount_type');
            $table->string('discount_from_amount')->nullable()->after('default_discount');
            $table->tinyInteger('isDiscountAutoApply')->default(0)->after('discount_from_amount');

            // Message Template fields
            $table->text('order_accepted')->nullable()->after('default_discount');
            $table->text('order_dispatched')->nullable()->after('order_accepted');
            $table->text('order_prepared')->nullable()->after('order_dispatched');
            $table->text('order_delivered')->nullable()->after('order_prepared');
            $table->text('review_template')->nullable()->after('order_delivered');
            $table->text('payment_received')->nullable()->after('review_template');
            $table->text('payment_failed')->nullable()->after('payment_received');
            $table->text('payment_refunded')->nullable()->after('payment_failed');
            $table->text('order_cancel')->nullable()->after('payment_refunded');
            $table->unsignedBigInteger('default_template_id')->nullable()->after('order_cancel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting_catalogs', function (Blueprint $table) {
            $table->dropColumn(['logo', 'business_name', 'business_address', 'business_phone', 'business_whatsapp', 'business_email', 'gstin_vat', 'currency_code', 'currency_symbol', 'discount_type', 'default_discount', 'order_accepted', 'order_dispatched', 'order_prepared', 'order_delivered', 'review_template', 'payment_received', 'payment_failed', 'payment_refunded', 'order_cancel', 'default_template_id']);
        });
    }
}
