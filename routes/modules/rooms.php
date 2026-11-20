<?php

use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');

Route::middleware('auth')->group(function () {
    Route::get('/management/rooms', [RoomController::class, 'manage'])->name('rooms.manage');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
});

Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
