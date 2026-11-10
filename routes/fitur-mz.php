<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\PostBiasaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'onboarded'])->group(function () {
    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
    Route::get('/dashboard', [BerandaController::class, 'index'])->name('dashboard');
    Route::get('/postingan/{postingan}', [BerandaController::class, 'show'])->name('postingan.show');

    // Fitur 3: Post Biasa
    Route::get('/post-biasa/buat', [PostBiasaController::class, 'create'])->name('post-biasa.create');
    Route::post('/post-biasa', [PostBiasaController::class, 'store'])->name('post-biasa.store');
});
