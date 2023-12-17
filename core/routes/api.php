<?php

use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// ROUTING API
use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\HrController;
use App\Http\Controllers\Api\KlienController;
use App\Http\Controllers\Api\KaryawanController;

Route::prefix('v1')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('login', 'login');
            Route::post('logout', 'logout');
        });
    });

    Route::controller(KaryawanController::class)->group(function () {
        Route::prefix('employee')->group(function () {
            Route::post('store', 'store')->middleware(['auth:sanctum', 'ability:karyawan:outsource']);
            Route::put('{id}/update', 'update')->middleware(['auth:sanctum']);
            Route::delete('{id}/delete', 'destroy')->middleware(['auth:sanctum']);
        });
    });
});
