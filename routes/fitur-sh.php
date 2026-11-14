<?php

use App\Http\Controllers\LamaranController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PelamarController;
use App\Http\Controllers\PostinganController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'onboarded'])->group(function () {
    // PCT-PC01: buat postingan cari tim
    Route::post('/postingan', [PostinganController::class, 'store'])->name('postingan.store');

    // PCT-PC01: rekomendasi kandidat untuk postingan cari tim
    Route::get('/postingan/{postingan}/rekomendasi', [PostinganController::class, 'rekomendasi'])->name('postingan.rekomendasi');

    // PCT-PC06: kirim lamaran (route kontrak untuk tombol Lamar milik MZ)
    Route::post('/postingan/{postingan_id}/lamaran', [LamaranController::class, 'store'])->name('lamaran.store');

    // Kelola pelamar: daftar, profil lengkap, terima, tolak (PCT-PC02, PC03, PC04)
    Route::get('/postingan/{postingan}/pelamar', [PelamarController::class, 'index'])->name('pelamar.index');
    Route::get('/lamaran/{lamaran}', [PelamarController::class, 'show'])->name('pelamar.show');
    Route::post('/lamaran/{lamaran}/terima', [PelamarController::class, 'terima'])->name('pelamar.terima');
    Route::post('/lamaran/{lamaran}/tolak', [PelamarController::class, 'tolak'])->name('pelamar.tolak');

    // Halaman Permintaan Kolaborasi dan tandai notifikasi sudah dibaca
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/dibaca-semua', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.dibaca-semua');
    Route::post('/notifikasi/{notifikasi}/dibaca', [NotifikasiController::class, 'tandaiDibaca'])->name('notifikasi.dibaca');
});
