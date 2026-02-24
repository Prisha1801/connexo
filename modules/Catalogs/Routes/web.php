<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within the group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(
    [
        'middleware' => ['web', 'impersonate'],
        'namespace' => 'Modules\Catalogs\Http\Controllers',
    ],
    function () {
        Route::group(
            [
                'middleware' => ['verified', 'web', 'auth', 'otp.verified', 'impersonate', 'XssSanitizer', 'isOwnerOnPro', 'Modules\Wpbox\Http\Middleware\CheckPlan'],
            ],
            function () {
                Route::prefix('catalog')->group(function () {
                    Route::Any('/list', 'Main@index')->name('catalog.index');
                    Route::get('/{Catalog}/edit', 'Main@edit')->name('catalog.edit');
                    Route::get('/create', 'Main@create')->name('catalog.create');
                    Route::post('/', 'Main@store')->name('catalog.store');
                    Route::put('/{Catalog}', 'Main@update')->name('catalog.update');
                    Route::get('/del/{Catalog}', 'Main@destroy')->name('catalog.delete');
                    Route::get('/loginas/{Catalog}', 'Main@loginas')->name('catalog.loginas');

                    Route::post('/fetch-catalog', 'Main@fetchCatalog')->name('catalog.fetchCatalog');
                    Route::Any('/product', 'Main@productsCatalog')->name('catalog.productsCatalog');
                    Route::Any('/catalogs-templates', 'Main@catalogsTemplatesIndex')->name('catalog.catalogsTemplatesIndex');
                    Route::Any('/catalogs-templates-create', 'Main@catalogsTemplatesCreate')->name('catalog.catalogsTemplatesCreate');
                    Route::Any('/carousel-templates-create', 'Main@carouselTemplatesCreate')->name('catalog.carouselTemplatesCreate');
                    Route::Any('catalogs-templates/upload-image', 'Main@uploadImage')->name('catalog.uploadImage');
                    Route::delete('catalogs-templates/del/{template}', 'Main@destroyCatalog')->name('catalog.destroyCatalog');
                    Route::Any('catalogs-templates/upload-video', 'Main@uploadVideo')->name('catalog.uploadVideo');
                    Route::Any('catalogs-templates/upload-pdf', 'Main@uploadPdf')->name('catalog.uploadPdf');
                    Route::Any('catalogs-templates/submit-catalogs', 'Main@submitCatalogTemplate')->name('catalog.submitCatalogTemplate');
                    Route::Any('catalogs-templates/catalog-template-message', 'Main@sendWhatsAppCatalogTemplateMessage')->name('catalog.sendWhatsAppCatalogTemplateMessage');
                    Route::delete('category/delete/{id}', 'Main@categoryDelete')->name('catalog.categoryDelete');

                    // order
                    Route::Any('order', 'Main@orderIndex')->name('catalog.orderIndex');
                    Route::Any('order/item/{id}', 'Main@itemIndex')->name('catalog.itemIndex');
                    Route::Any('order/edit/{id}', 'Main@itemEdit')->name('catalog.itemEdit');
                    Route::Any('order/update/{id}', 'Main@orderUpdate')->name('catalog.orderUpdate');
                    Route::Any('order/pdf/{id}', 'Main@pdf')->name('catalog.pdf');
                    Route::get('order/receipt/{id}', 'Main@printReceipt')->name('catalog.receipt');

                    Route::Any('payment/setting', 'Main@setting')->name('catalog.setting');
                    Route::Any('payment/setting/update', 'Main@settingUpdate')->name('catalog.settingUpdate');
                    Route::Any('payment/setting/whatsapp_update', 'Main@whatsappPhoneUpdate')->name('catalog.whatsappPhoneUpdate');

                    // category
                    Route::Any('category/index', 'Main@categoryIndex')->name('catalog.categoryIndex');
                    Route::Any('category/create', 'Main@categoryCreate')->name('catalog.categoryCreate');
                    Route::Any('category/store', 'Main@categoryStore')->name('catalog.categoryStore');
                    Route::Any('category/edit/{id}', 'Main@categoryEdit')->name('catalog.categoryEdit');
                    Route::Any('category/update/{id}', 'Main@categoryUpdate')->name('catalog.categoryUpdate');

                    Route::Any('product/edit/{id}', 'Main@productEdit')->name('catalog.productEdit');
                    Route::Any('product/update', 'Main@productUpdate')->name('catalog.productUpdate');

                    Route::Any('/address-message-enable', 'Main@toggleAddressMessage')->name('catalog.toggleAddressMessage');
                    Route::Any('/payment-method-enable', 'Main@togglePaymentMethod')->name('catalog.togglePaymentMethod');
                    Route::post('/send-order-dispatch/{order_id}', 'Main@sendOrderDispatch')->name('catalog.sendOrderDispatch');
                    Route::post('/orders/update-status', 'Main@updateStatus')->name('catalog.orderStatusUpdate');
                    Route::post('/orders/{order}/update-shipping', 'Main@updateShipping')->name('catalog.updateShipping');
                    Route::post('/orders/update-payment/{id}', 'Main@updatePayment')->name('catalog.updatePayment');
                    Route::post('/send-payment-whatsapp', 'Main@sendPaymentWhatsApp')->name('catalog.sendPaymentWhatsApp');
                    Route::post('/orders/cancel/{id}', 'Main@cancelOrder')->name('catalog.cancelOrder');
                    Route::post('/orders/{id}/update-contact', 'Main@updateContact')->name('catalog.updateContact');
                    Route::post('/orders/{id}/update-note', 'Main@updateOrderNote')->name('catalog.updateOrderNote');
                    Route::post('/apply-discount/{order}', 'Main@applyDiscount')->name('catalog.applyDiscount');
                    Route::post('/resend-payment-link', 'Main@resendPaymentLink')->name('catalog.resendPaymentLink');

                    Route::post('/order/{order}/resend-address-form', 'Main@resendAddressForm')->name('order.resendAddressForm');

                    Route::post('/storefront-update', 'Main@storefrontUpdate')->name('catalog.storefrontUpdate');
                    Route::post('/templates-update', 'Main@templatesUpdate')->name('catalog.templatesUpdate');
                    Route::post('/order/{id}/send-default-template', 'Main@sendDefaultTemplate')->name('catalog.sendDefaultTemplate');
                    Route::post('/order/{id}/refresh-window', 'Main@refreshWindowStatus')->name('catalog.refreshWindowStatus');
                    Route::post('/order/{id}/send-template', 'Main@sendTemplateMessage')->name('catalog.sendTemplateMessage');
                    Route::post('/order/{id}/add-item', 'Main@addItemToOrder')->name('catalog.addItemToOrder');
                    Route::post('/order/{id}/send-updated-cart', 'Main@sendUpdatedCart')->name('catalog.sendUpdatedCart');
                    Route::get('/order/{order}/items-section', 'Main@orderItemsSection')->name('catalog.orderItemsSection');
                    Route::post('/order/{order}/update-item', 'Main@updateOrderItem')->name('catalog.updateOrderItem');
                    Route::post('/order/{order}/delete-item', 'Main@deleteOrderItem')->name('catalog.deleteOrderItem');

                    Route::get('/order/{id}/adjust-section', 'Main@orderAdjustSection')
                        ->name('catalog.orderAdjustSection');
                });
            },
        );
    },
);
