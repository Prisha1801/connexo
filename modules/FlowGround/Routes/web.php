<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'middleware' => ['web', 'auth', 'verified', 'impersonate'],
    'namespace'  => 'Modules\FlowGround\Http\Controllers',
    'prefix'     => 'flow-ground',
    'as'         => 'flow-ground.',
], function () {
    Route::get('/', 'FlowGroundController@index')->name('index');
    Route::get('create', 'FlowGroundController@create')->name('create');
    Route::post('store', 'FlowGroundController@store')->name('store');
    Route::get('sync', 'FlowGroundController@sync')->name('sync');
    Route::get('view-data', 'FlowGroundController@viewData')->name('view_data');
    Route::get('{flowGround}/preview', 'FlowGroundController@preview')->name('preview');
    Route::get('{flowGround}/publish', 'FlowGroundController@publish')->name('publish');
    Route::get('{flowGround}/data', 'FlowGroundController@showData')->name('data');
    Route::get('{flowGround}/edit', 'FlowGroundController@edit')->name('edit');
    Route::put('{flowGround}', 'FlowGroundController@update')->name('update');
    Route::delete('{flowGround}', 'FlowGroundController@destroy')->name('destroy');
    // Legacy (avoid breaking any old links)
    Route::delete('del/{flowGround}', 'FlowGroundController@destroy')->name('destroy_legacy');
});
