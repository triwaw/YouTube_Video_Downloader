<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Users
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DownloadController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Download Creation Workflow
    |--------------------------------------------------------------------------
    */

    Route::post('/analyze', [DownloadController::class, 'analyze'])
        ->name('download.analyze');

    Route::post('/download', [DownloadController::class, 'store'])
        ->name('download.store');

    /*
    |--------------------------------------------------------------------------
    | Download Monitoring
    |--------------------------------------------------------------------------
    */

    Route::get('/downloads/{download}', [DownloadController::class, 'show'])
        ->name('downloads.show');

    /*
    |--------------------------------------------------------------------------
    | Download History
    |--------------------------------------------------------------------------
    */

    Route::get('/downloads', [DownloadController::class, 'history'])
        ->name('downloads.index');

    /*
    |--------------------------------------------------------------------------
    | Download File
    |--------------------------------------------------------------------------
    */


	Route::get('/downloads/{download}/file', [DownloadController::class, 'file']
		)->name('downloads.file');


    /*
    |--------------------------------------------------------------------------
    | Download Status : Create JSON Status Endpoint
    |--------------------------------------------------------------------------
    */
	
	
	Route::get(
			'/downloads/{download}/status',
			[DownloadController::class, 'status']
		)->name('downloads.status');	
		
    /*
    |--------------------------------------------------------------------------
    | Delete Download
    |--------------------------------------------------------------------------
    */

    Route::delete('/downloads/{download}', [DownloadController::class, 'destroy'])
        ->name('downloads.destroy');

    /*
    |--------------------------------------------------------------------------
    | User Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';