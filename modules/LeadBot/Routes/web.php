<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\Route;
use Modules\LeadBot\Http\Controllers\LeadBotController;
use Modules\LeadBot\Http\Controllers\LeadBotWebhookController;

// Incoming webhook endpoint (no CSRF)
Route::post('/api/lead-bot/webhook/{token}', [LeadBotWebhookController::class, 'handle'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// UI routes
Route::group([
    'middleware' => ['web', 'auth', 'verified', 'impersonate'],
    'prefix' => 'lead-bot',
    'as' => 'lead-bot.',
], function () {
    Route::get('/', [LeadBotController::class, 'index'])->name('index');
    Route::get('/create', [LeadBotController::class, 'create'])->name('create');
    Route::post('/', [LeadBotController::class, 'store'])->name('store');
    Route::get('/{leadBot}/edit', [LeadBotController::class, 'edit'])->name('edit');
    Route::put('/{leadBot}', [LeadBotController::class, 'update'])->name('update');
    Route::delete('/{leadBot}', [LeadBotController::class, 'destroy'])->name('destroy');

    Route::get('/webhooks/{leadBot}/latest', [LeadBotWebhookController::class, 'latestVariables'])->name('webhooks.latest');
});

