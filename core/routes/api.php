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
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\JamKerjaController;
use App\Http\Controllers\Api\TimesheetController;
use App\Http\Controllers\Api\ReportController;

Route::prefix('v1')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::prefix('auth')->group(function () {
            Route::get('profile', 'profile')->middleware(['auth:sanctum']);
            Route::post('login', 'login');
        });
    });

    Route::controller(EmployeeController::class)->group(function () {
        Route::prefix('employee')->group(function () {
            Route::get('lists', 'lists')->middleware(['auth:sanctum']);
            Route::get('get-list-clients', 'getListClients')->middleware(['auth:sanctum']);

            Route::post('store', 'store')->middleware(['auth:sanctum']);
            Route::get('edit/{id}', 'edit')->middleware(['auth:sanctum']);
            Route::put('{id}/update', 'update')->middleware(['auth:sanctum']);
            Route::delete('{id}/destroy', 'destroy')->middleware(['auth:sanctum']);
        });
    });

    Route::controller(ClientController::class)->group(function () {
        Route::prefix('client')->group(function () {
            Route::get('lists', 'lists')->middleware(['auth:sanctum']);

            Route::post('store', 'store')->middleware(['auth:sanctum']);
            Route::get('edit/{id}', 'edit')->middleware(['auth:sanctum']);
            Route::put('{id}/update', 'update')->middleware(['auth:sanctum']);
            Route::delete('{id}/destroy', 'destroy')->middleware(['auth:sanctum']);
        });
    });

    Route::controller(JamKerjaController::class)->group(function () {
        Route::prefix('shift')->group(function () {
            Route::get('/presensi/work-hour/{dateDay}', 'getPresensi')->middleware(['auth:sanctum']);

            Route::get('/presensi/employee/{dateDay}', 'getPresensiEmployee')->middleware(['auth:sanctum']);
            Route::post('/presensi/employee/{dateDay}', 'postPresensiEmployee')->middleware(['auth:sanctum']);
        });
    });

    Route::controller(TimesheetController::class)->group(function () {
        Route::prefix('timesheet')->group(function () {
            Route::get('lists', 'lists')->middleware(['auth:sanctum']);
            Route::get('/employee', 'getTimesheetEmployee')->middleware(['auth:sanctum']);

            Route::post('/store', 'store')->middleware(['auth:sanctum']);
            Route::post('/approve', 'approve')->middleware(['auth:sanctum']);
            Route::post('/reject', 'reject')->middleware(['auth:sanctum']);
        });
    });

    Route::controller(ReportController::class)->group(function () {
        Route::prefix('report')->group(function () {

        });
    });

});
