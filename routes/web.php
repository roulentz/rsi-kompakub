<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Placeholder Beranda; MZ mengganti isinya, nama route "dashboard" jangan diubah.
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'onboarded'])->name('dashboard');

// Pengaturan akun bawaan Breeze (ganti nama dan kata sandi, hapus akun).
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Memuat otomatis fitur-cv.php, fitur-sh.php, fitur-mz.php, fitur-gp.php.
// File ini tidak perlu diedit lagi saat anggota lain menambah file route-nya.
foreach (glob(__DIR__.'/fitur-*.php') as $fileFitur) {
    require $fileFitur;
}
