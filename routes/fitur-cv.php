<?php

use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PortofolioController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ProyekSelesaiController;
use App\Http\Controllers\RekomendasiController;
use App\Http\Controllers\VerifikasiController;
use Illuminate\Support\Facades\Route;

// Verifikasi kode 6 digit (pengguna belum login)
Route::middleware('guest')->group(function () {
    Route::get('verifikasi', [VerifikasiController::class, 'form'])->name('verifikasi.form');
    Route::post('verifikasi', [VerifikasiController::class, 'proses'])->middleware('throttle:6,1')->name('verifikasi.proses');
    Route::post('verifikasi/kirim-ulang', [VerifikasiController::class, 'kirimUlang'])->middleware('throttle:3,1')->name('verifikasi.kirim-ulang');
});

Route::middleware('auth')->group(function () {
    Route::get('onboarding', [OnboardingController::class, 'index'])->name('onboarding.form');
    Route::post('onboarding', [OnboardingController::class, 'proses'])->name('onboarding.proses');

    Route::middleware('onboarded')->group(function () {
        Route::get('profil', [ProfilController::class, 'show'])->name('profil.show');
        Route::get('profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
        Route::put('profil', [ProfilController::class, 'update'])->name('profil.update');

        Route::get('profil/portofolio', [PortofolioController::class, 'edit'])->name('portofolio.edit');
        Route::post('profil/portofolio', [PortofolioController::class, 'store'])->name('portofolio.store');
        Route::delete('profil/portofolio/{portofolio}', [PortofolioController::class, 'destroy'])->name('portofolio.destroy');
        Route::patch('profil/portofolio/{portofolio}/geser', [PortofolioController::class, 'geser'])->name('portofolio.geser');

        Route::get('profil/{profil}', [ProfilController::class, 'lihat'])->whereNumber('profil')->name('profil.lihat');
        Route::post('profil/{profil}/rekomendasi', [RekomendasiController::class, 'store'])->whereNumber('profil')->name('rekomendasi.store');
        Route::post('postingan/{postingan}/selesai', ProyekSelesaiController::class)->name('postingan.selesai');
        Route::post('rekomendasi/{rekomendasi}/laporkan', [RekomendasiController::class, 'laporkan'])->name('rekomendasi.laporkan');
    });

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('rekomendasi', [RekomendasiController::class, 'laporan'])->name('rekomendasi.index');
        Route::patch('rekomendasi/{rekomendasi}', [RekomendasiController::class, 'tinjau'])->name('rekomendasi.tinjau');
    });
});
