<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DownloadController;
use App\Models\Download;
use App\Services\YoutubeDownloaderService;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DownloadController::class, 'index'])
        ->name('dashboard');

    Route::post('/analyze', [DownloadController::class, 'analyze'])
        ->name('download.analyze');

    Route::post('/download', [DownloadController::class, 'store'])
        ->name('download.store');

    Route::get('/downloads/{download}', [DownloadController::class, 'show'])
        ->name('downloads.show');
});

require __DIR__.'/auth.php';