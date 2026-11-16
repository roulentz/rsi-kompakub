<?php
use App\Http\Controllers\ModerasiController;
use App\Http\Controllers\DMController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;


Route::middleware('auth')->group(function () {
    // DM
    Route::get('/dm', [DMController::class, 'index'])->name('dm.index');
    Route::get('/dm/mulai/{user}', [DMController::class, 'mulai'])->name('dm.mulai');
    Route::get('/dm/{percakapan}', [DMController::class, 'show'])->name('dm.show');
    Route::post('/dm/{percakapan}/pesan', [DMController::class, 'kirim'])->name('dm.kirim');
    Route::get('/dm/{percakapan}/pesan', [DMController::class, 'pesanJson'])->name('dm.pesan');
    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    //Moderasi
    Route::middleware(['auth', 'admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::delete('/postingan/{postingan}', [ModerasiController::class, 'destroy'])
                ->name('postingan.destroy');
        });

});