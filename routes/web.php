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

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Ruta destino temporal para Supplier (se reemplazará en Etapa 6)
    Route::get('/suppliers/dashboard', function () {
        return view('dashboard');
    })->middleware('role:Supplier')->name('suppliers.dashboard');
});