<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use Modules\CTWA\Http\Controllers\CTWAController;



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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [LoginController::class, 'login']);
Route::post('/v2/login', [LoginController::class, 'login']);
Route::match(['get', 'post'], 'webhook/wpbox/receive/{token}', [CTWAController::class, 'receive']);
Route::get('/leads', [CTWAController::class, 'viewLeads']);
