<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Registro explícito de rutas modulares (Módulo Jesús Fernández)
require __DIR__.'/modules/auth.php';
require __DIR__.'/modules/admin.php';
require __DIR__.'/modules/supplier.php';
require __DIR__.'/modules/rooms.php';
require __DIR__.'/modules/bookings.php';

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
