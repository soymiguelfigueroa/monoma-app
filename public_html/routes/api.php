<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth', [AuthController::class, 'auth']);

Route::group(['middleware' => ['api']], function () {
    Route::post('/lead', [LeadController::class, 'create']);
    Route::get('/lead/{id}', [LeadController::class, 'get']);
    
    Route::get('/leads', [LeadsController::class, 'get']);
});
