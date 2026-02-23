<?php

use Illuminate\Support\Facades\Route;
use Modules\LeadManager\Http\Controllers\LeadController;

Route::group([
    'middleware' => ['web', 'auth', 'verified', 'impersonate'],
    'prefix' => 'lead-manager',
    'as' => 'lead-manager.',
], function () {
    Route::get('/', [LeadController::class, 'index'])->name('index');
    Route::get('/kanban-data', [LeadController::class, 'kanbanData'])->name('kanban-data');
    Route::get('/kanban', [LeadController::class, 'kanban'])->name('kanban');
    Route::post('/stages', [LeadController::class, 'storeStage'])->name('stages.store');
    Route::get('/create', [LeadController::class, 'create'])->name('create');
    Route::post('/', [LeadController::class, 'store'])->name('store');
    Route::get('/import', [LeadController::class, 'importForm'])->name('import');
    Route::post('/import', [LeadController::class, 'importProcess'])->name('import.process');
    Route::post('/assign', [LeadController::class, 'assign'])->name('assign');
    Route::get('/{lead}', [LeadController::class, 'show'])->name('show');
    Route::get('/{lead}/edit', [LeadController::class, 'edit'])->name('edit');
    Route::put('/{lead}', [LeadController::class, 'update'])->name('update');
    Route::post('/{lead}/notes', [LeadController::class, 'storeNote'])->name('notes.store');
    Route::match(['post', 'patch'], '/{lead}/stage', [LeadController::class, 'updateStage'])->name('update-stage');
    Route::delete('/{lead}', [LeadController::class, 'destroy'])->name('destroy');
});
