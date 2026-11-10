<?php

use App\Http\Controllers\DMController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // DM-PC01: daftar percakapan, mulai percakapan, kirim dan ambil pesan
    Route::get('/dm', [DMController::class, 'index'])->name('dm.index');
    Route::get('/dm/mulai/{user}', [DMController::class, 'mulai'])->name('dm.mulai');
    Route::get('/dm/{percakapan}', [DMController::class, 'show'])->name('dm.show');
    Route::post('/dm/{percakapan}/pesan', [DMController::class, 'kirim'])->name('dm.kirim');
    Route::get('/dm/{percakapan}/pesan', [DMController::class, 'pesanJson'])->name('dm.pesan');
});