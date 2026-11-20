<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Registro explícito de rutas modulares (Módulo Jesús Fernández)
require __DIR__ . '/modules/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rutas destino temporales para redirección por rol (se reemplazarán en Etapas 3, 5 y 6)
    Route::get('/admin/dashboard', function () {
        return view('dashboard');
    })->name('admin.dashboard');

    Route::get('/suppliers/dashboard', function () {
        return view('dashboard');
    })->name('suppliers.dashboard');
});