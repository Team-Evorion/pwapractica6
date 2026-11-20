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

    // Ruta destino protegida por rol Administrator (se completará en Etapas 4 y 5)
    Route::get('/admin/dashboard', function () {
        return view('dashboard');
    })->middleware('role:Administrator')->name('admin.dashboard');

    // Ruta destino protegida por rol Supplier (se completará en Etapa 6)
    Route::get('/suppliers/dashboard', function () {
        return view('dashboard');
    })->middleware('role:Supplier')->name('suppliers.dashboard');
});