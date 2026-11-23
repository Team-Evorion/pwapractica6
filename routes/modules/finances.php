<?php

use App\Http\Controllers\BookingPaymentController;
use App\Http\Controllers\FinanceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/finances', [FinanceController::class, 'index'])->name('finances.index');
    Route::post('/finances/bookings/{booking}/payments', [BookingPaymentController::class, 'store'])
        ->name('finances.payments.store');
});
