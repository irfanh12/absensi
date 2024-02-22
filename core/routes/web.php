<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('health');
});

Route::get('/test', function () {
    // Start date
    $start_date = strtotime('2024-02-01');

    // End date
    $end_date = strtotime('2024-02-29 23:59:59');

    // Generate epoch timestamps for each day within the specified range
    $current_date = $start_date;
    while ($current_date <= $end_date) {
        echo $current_date . "\n";
        // Increment current date by 1 day (86400 seconds)
        $current_date += 86400;
    }
});
