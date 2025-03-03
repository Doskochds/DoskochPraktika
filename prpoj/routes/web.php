<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OneTimeLinkController;
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
    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::get('/files/create', [FileController::class, 'create'])->name('files.create');
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::get('/files/{id}', [FileController::class, 'show'])->name('files.show');
    Route::delete('/files/{id}', [FileController::class, 'destroy'])->name('files.destroy');
});
Route::get('/file/{id}', [FileController::class, 'view'])->name('files.view');
Route::delete('/file/{token}/delete', [OneTimeLinkController::class, 'deleteLink'])->name('file.delete.link');
Route::post('/files/{file}/generate-one-time-link', [OneTimeLinkController::class, 'generate'])->name('file.generate.one');
Route::get('/files/view/{token}', [OneTimeLinkController::class, 'view'])->name('file.views.one');
Route::get('/statistics', [FileController::class, 'statistics'])->middleware(['auth'])->name('files.statistics');

require __DIR__.'/auth.php';
