<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Registro explícito de rutas modulares (Módulo Jesús Fernández)
require __DIR__ . '/modules/auth.php';
require __DIR__ . '/modules/admin.php';
require __DIR__ . '/modules/supplier.php';

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});