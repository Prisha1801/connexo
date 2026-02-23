<?php

use Illuminate\Support\Facades\Route;
use Modules\CTWA\Http\Controllers\CTWAController;

// Webhook routes (no auth, no CSRF)
Route::get('/ctwa/webhook/{token}', [CTWAController::class, 'verify']);
Route::post('/ctwa/webhook/{token}', [CTWAController::class, 'receive'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('webhook.ctwa');

Route::get('/ctwa/campaigns', [CTWAController::class, 'listCampaigns']);
Route::get('/getPayMetaData', [CTWAController::class, 'getPayMetaData']);

// Authenticated CTWA routes
Route::middleware(['web', 'auth', 'impersonate'])->group(function () {
    Route::prefix('ctwa')->name('ctwa.')->group(function () {
        Route::get('/', [CTWAController::class, 'index'])->name('index');
        Route::get('/panel', [CTWAController::class, 'panel'])->name('panel');
        Route::get('/leads', [CTWAController::class, 'leadsIndex'])->name('leads');
        Route::get('/create_ads', [CTWAController::class, 'create_ads'])->name('create_ads');
        Route::get('/fetch-ads', [CTWAController::class, 'fetchAds'])->name('fetch_ads');
        Route::get('/fetch-store', [CTWAController::class, 'fetchAndStoreAds'])->name('fetch_store_ads');
    });
    Route::get('/leads/filter', [CTWAController::class, 'filter']);

    Route::prefix('meta')->group(function () {
        Route::get('/countries', [CTWAController::class, 'getCountries']);
        Route::get('/locations', [CTWAController::class, 'getLocations']);
        Route::get('/meta-interests', [CTWAController::class, 'searchMetaInterests']);
        Route::get('/pages', [CTWAController::class, 'getUserPages']);
        Route::post('/page-profile', [CTWAController::class, 'getMetaProfileFromSelection']);
        Route::get('/ad-accounts', [CTWAController::class, 'getMetaAdAccounts']);
    });

    Route::post('/meta/ads/create', [CTWAController::class, 'submitCtwaAd'])->name('ctwa.create');
    Route::get('/ad-details/{adId}', [CTWAController::class, 'show'])->name('ad.details');
    Route::get('/ads/{ad}', [CTWAController::class, 'show'])->name('ads.show');
    Route::post('/campaigns/send', [CTWAController::class, 'sendCampaign'])->name('campaign.send');
});
