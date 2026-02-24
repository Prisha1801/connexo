<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryAgentIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_agent_id')) {
                $table->unsignedBigInteger('delivery_agent_id')->nullable()->after('delivery_partner');
                $table->index('delivery_agent_id');
            }
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
            if (Schema::hasColumn('orders', 'delivery_agent_id')) {
                $table->dropIndex(['delivery_agent_id']);
                $table->dropColumn('delivery_agent_id');
            }
        });
    }
}

