<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\WorkFlows\Http\Controllers\WorkflowWebhookController;

Route::post('/webhook/{workflow}', [WorkflowWebhookController::class, 'capture'])->name('workflow_webhooks.capture');
Route::post('/api/webhook/{token}', [WorkflowWebhookController::class, 'handleWebhook']);


