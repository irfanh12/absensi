<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JamKerjaController;
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

// ROUTING API
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\KlienController;
use App\Http\Controllers\Api\TimesheetController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::prefix('auth')->group(function () {
            Route::get('profile', 'profile')->middleware(['auth:sanctum']);
            Route::post('login', 'login');
        });
    });

    Route::controller(KaryawanController::class)->group(function () {
        Route::prefix('employee')->group(function () {
            Route::get('lists', 'lists')->middleware(['auth:sanctum']);
            Route::get('get-list-clients', 'getListClients')->middleware(['auth:sanctum']);

            // Select 2 Data
            Route::get('search', 'search')->middleware(['auth:sanctum']);

            Route::post('store', 'store')->middleware(['auth:sanctum']);
            Route::get('edit/{id}', 'edit')->middleware(['auth:sanctum']);
            Route::put('{id}/update', 'update')->middleware(['auth:sanctum']);
            Route::delete('{id}/destroy', 'destroy')->middleware(['auth:sanctum']);
        });
    });

    Route::controller(KlienController::class)->group(function () {
        Route::prefix('client')->group(function () {
            Route::get('lists', 'lists')->middleware(['auth:sanctum']);

            Route::post('store', 'store')->middleware(['auth:sanctum']);
            Route::get('edit/{id}', 'edit')->middleware(['auth:sanctum']);
            Route::put('{id}/update', 'update')->middleware(['auth:sanctum']);
            Route::delete('{id}/destroy', 'destroy')->middleware(['auth:sanctum']);
        });
    });

    Route::controller(JamKerjaController::class)->group(function () {
        Route::prefix('presensi')->group(function () {
            Route::get('lists/{dateDay}', 'lists')->middleware(['auth:sanctum']);

            Route::get('work-hour/{dateDay}', 'getPresensi')->middleware(['auth:sanctum']);

            Route::get('employee/{dateDay}', 'getPresensiEmployee')->middleware(['auth:sanctum']);
            Route::post('employee/{dateDay}', 'postPresensiEmployee')->middleware(['auth:sanctum']);
            Route::post('report/{dateDay}', 'report')->middleware(['auth:sanctum']);
        });
    });

    Route::controller(TimesheetController::class)->group(function () {
        Route::prefix('timesheet')->group(function () {
            Route::get('lists/{dateDay}', 'lists')->middleware(['auth:sanctum']);
            Route::get('employee', 'getTimesheetEmployee')->middleware(['auth:sanctum']);

            Route::post('store', 'store')->middleware(['auth:sanctum']);
            Route::post('approve/{dateDay}', 'approve')->middleware(['auth:sanctum']);
            Route::post('reject/{dateDay}', 'reject')->middleware(['auth:sanctum']);
            Route::post('report/{dateDay}', 'report')->middleware(['auth:sanctum']);
        });
    });

});

Route::get('health', function () {
    return response()->json(['status' => 'ok'], 200);
})->name('health');
