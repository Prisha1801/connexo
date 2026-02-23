<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\Route;
use Modules\WorkFlows\Http\Controllers\WorkflowController;
use Modules\WorkFlows\Http\Controllers\WorkflowWebhookController;

// Webhook endpoints (no auth, no CSRF for external callers)
Route::post('/webhook/{workflow}', [WorkflowWebhookController::class, 'capture'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('workflow_webhooks.capture');
Route::post('/api/webhook/{token}', [WorkflowWebhookController::class, 'handleWebhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Fetch webhook response for mapping variables (auth required)
Route::get('/workflow-webhooks/{workflowId}', [WorkflowWebhookController::class, 'fetchWebhookResponse'])
    ->middleware(['web', 'auth', 'verified', 'impersonate'])
    ->name('workflow_webhooks.fetch');

// UI routes
Route::group([
    'middleware' => ['web', 'auth', 'verified', 'impersonate'],
    'prefix' => 'workflows',
    'as' => 'workflows.',
], function () {
    Route::get('/', [WorkflowController::class, 'index'])->name('index');
    Route::get('/create', [WorkflowController::class, 'create'])->name('create');
    Route::post('/', [WorkflowController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [WorkflowController::class, 'edit'])->name('edit');
    Route::get('/{workflowId}/task-form/{taskType}/{index}', [WorkflowController::class, 'getTaskForm'])->name('task-form');
    Route::put('/{id}', [WorkflowController::class, 'update'])->name('update');
    Route::delete('/{workflow}', [WorkflowController::class, 'destroy'])->name('destroy');
});


