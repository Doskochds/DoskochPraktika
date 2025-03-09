<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiFileController;
use App\Http\Controllers\ApiOneTimeLinkController;


Route::post('/register', [ApiAuthController::class, 'register'])->name('api.register');
Route::post('/login', [ApiAuthController::class, 'login'])->name('api.login');


Route::get('/health-check', function () {
return response()->json([
'status' => 'ok',
'message' => 'API is up and running',
'timestamp' => now(),
]);
})->name('api.health-check');


Route::middleware('auth')->group(function () {


Route::get('/files', [ApiFileController::class, 'index'])->name('api.files.index');
Route::post('/files', [ApiFileController::class, 'store'])->name('api.files.store');
Route::get('/files/{id}', [ApiFileController::class, 'show'])->name('api.files.show');
Route::delete('/files/{id}', [ApiFileController::class, 'destroy'])->name('api.files.destroy');
Route::get('/files/view/{id}', [ApiFileController::class, 'view'])->name('api.files.view');
Route::get('/files/statistics', [ApiFileController::class, 'statistics'])->name('api.files.statistics');

Route::post('/files/{id}/generate-links', [ApiOneTimeLinkController::class, 'generate'])->name('api.links.generate');
Route::get('/links/{token}', [ApiOneTimeLinkController::class, 'view'])->name('api.links.view');
Route::delete('/links/{token}', [ApiOneTimeLinkController::class, 'deleteLink'])->name('api.links.delete');
Route::delete('/links/cleanup', [ApiOneTimeLinkController::class, 'cleanUpLinks'])->name('api.links.cleanup');

Route::post('/logout', [ApiAuthController::class, 'logout'])->name('api.logout');
});

