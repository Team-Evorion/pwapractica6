<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])
        ->name('bookings.index');

    Route::post('/bookings', [BookingController::class, 'store'])
        ->name('bookings.store');

    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])
        ->name('bookings.status.update');
});
