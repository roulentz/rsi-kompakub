<?php

use App\Http\Controllers\LamaranController;
use App\Http\Controllers\PostinganController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // PCT-PC01: buat postingan cari tim
    Route::post('/postingan', [PostinganController::class, 'store'])->name('postingan.store');

    // PCT-PC01: rekomendasi kandidat untuk postingan cari tim
    Route::get('/postingan/{postingan}/rekomendasi', [PostinganController::class, 'rekomendasi'])->name('postingan.rekomendasi');

    // PCT-PC06: kirim lamaran (route kontrak untuk tombol Lamar milik MZ)
    Route::post('/postingan/{postingan_id}/lamaran', [LamaranController::class, 'store'])->name('lamaran.store');
});
