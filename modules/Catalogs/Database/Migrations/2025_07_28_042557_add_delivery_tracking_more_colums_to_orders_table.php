<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeliveryTrackingMoreColumsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::table('orders', function (Blueprint $table) {
            $table->string('for_person', 191)->nullable()->after('tax_value');
            $table->string('for_person_number', 191)->nullable()->after('for_person');
            $table->tinyInteger('isOrderConfirmed')->nullable()->after('status');
            $table->string('discount_percent')->nullable()->after('tax_offset');
            $table->enum('discount_type', ['percent', 'fixed'])->nullable()->after('discount_percent');
            $table->decimal('discount', 10, 2)->default(0)->after('discount_type');
            $table->decimal('discount_amount', 10, 2)->nullable()->after('discount');
            $table->decimal('final_amount', 10, 2)->nullable()->after('discount_amount');
            $table->string('shipping_method', 191)->nullable()->after('type');
            $table->string('tracking_number', 191)->nullable()->after('shipping_method');
            $table->string('delivery_partner', 191)->nullable()->after('tracking_number');
            $table->text('delivery_note')->nullable()->after('delivery_partner');
            $table->timestamp('delivery_datetime')->nullable(false)->after('delivery_note');
            $table->tinyInteger('feedback_msg')->default(0)->after('delivery_datetime');
            $table->text('cancel_reason')->nullable()->after('feedback_msg');
            $table->timestamp('canceled_at')->nullable()->after('cancel_reason');
            $table->text('order_process_note')->nullable()->after('cancel_reason');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'discount_percent',
                'shipping_method',
                'tracking_number',
                'delivery_partner',
                'delivery_note',
                'delivery_datetime',
                'feedback_msg',
                'cancel_reason',
                'canceled_at'
            ]);
        });
    }
}
