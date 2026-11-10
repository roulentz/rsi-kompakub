<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\PostBiasaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [BerandaController::class, 'index'])->name('beranda');
});