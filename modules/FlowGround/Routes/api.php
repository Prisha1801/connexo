<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/flow-ground', function (Request $request) {
    return $request->user();
});

Route::post('/whatsapp/flow/webhook', [
    \Modules\FlowGround\Http\Controllers\WhatsAppWebhookController::class,
    'handle'
]);