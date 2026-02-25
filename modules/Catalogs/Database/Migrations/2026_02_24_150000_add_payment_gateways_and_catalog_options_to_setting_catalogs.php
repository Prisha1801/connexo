<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentGatewaysAndCatalogOptionsToSettingCatalogs extends Migration
{
    public function up()
    {
        Schema::table('setting_catalogs', function (Blueprint $table) {
            if (!Schema::hasColumn('setting_catalogs', 'payment_configuration_payu')) {
                $table->text('payment_configuration_payu')->nullable()->after('payment_configuration_other');
            }
            if (!Schema::hasColumn('setting_catalogs', 'payment_configuration_zaakpay')) {
                $table->text('payment_configuration_zaakpay')->nullable()->after('payment_configuration_payu');
            }
            if (!Schema::hasColumn('setting_catalogs', 'payment_configuration_meta')) {
                $table->text('payment_configuration_meta')->nullable()->after('payment_configuration_zaakpay');
            }
            if (!Schema::hasColumn('setting_catalogs', 'low_stock_alert_at')) {
                $table->unsignedSmallInteger('low_stock_alert_at')->nullable()->after('default_template_id');
            }
            if (!Schema::hasColumn('setting_catalogs', 'allow_backorders')) {
                $table->boolean('allow_backorders')->default(false)->after('low_stock_alert_at');
            }
            if (!Schema::hasColumn('setting_catalogs', 'catalog_tagline')) {
                $table->string('catalog_tagline', 255)->nullable()->after('allow_backorders');
            }
        });
    }

    public function down()
    {
        Schema::table('setting_catalogs', function (Blueprint $table) {
            $cols = ['payment_configuration_payu', 'payment_configuration_zaakpay', 'payment_configuration_meta', 'low_stock_alert_at', 'allow_backorders', 'catalog_tagline'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('setting_catalogs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
