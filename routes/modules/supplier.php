<?php

use App\Http\Controllers\Supplier\SupplierDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Supplier'])
    ->prefix('suppliers')
    ->name('suppliers.')
    ->group(function () {
        Route::get('/dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');
        Route::post('/supplies', [SupplierDashboardController::class, 'store'])->name('supplies.store');
        Route::get('/supplies/{supply}/edit', [SupplierDashboardController::class, 'edit'])->name('supplies.edit');
        Route::put('/supplies/{supply}', [SupplierDashboardController::class, 'update'])->name('supplies.update');
        Route::delete('/supplies/{supply}', [SupplierDashboardController::class, 'destroy'])->name('supplies.destroy');
    });