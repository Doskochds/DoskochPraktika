<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiFileController;
use App\Http\Controllers\ApiOneTimeLinkController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Http\Request;

Route::post('/register', [ApiAuthController::class, 'register'])->name('api.register');
Route::post('/auth/login', [ApiAuthController::class, 'login'])->name('api.auth.login');
Route::get('/auth/login', [ApiAuthController::class, 'loginForm'])->name('api.auth.loginForm');
Route::post('/files/{id}/generate-links', [ApiOneTimeLinkController::class, 'generate'])->name('api.links.generate');
Route::get('/links/{token}', [ApiOneTimeLinkController::class, 'view'])->name('api.links.view');
Route::delete('/links/{token}', [ApiOneTimeLinkController::class, 'deleteLink'])->name('api.links.delete');
Route::delete('/links/cleanup', [ApiOneTimeLinkController::class, 'cleanUpLinks'])->name('api.links.cleanup');
Route::get('/files/{fileId}/links', [ApiOneTimeLinkController::class, 'getLinksByFileId'])->name('api.links.by-file');
Route::get('/sanctum/csrf-cookie', function () {
return response()->json(['message' => 'CSRF token set']);
})->name('api.csrf-cookie');
Route::get('/health-check', function () {
return response()->json([
'status' => 'ok',
'message' => 'API is up and running',
'timestamp' => now(),
]);
})->name('api.health-check');
Route::middleware([
    EnsureFrontendRequestsAreStateful::class,
    'auth:sanctum',
])->group(function () {
Route::get('/files', [ApiFileController::class, 'index'])->name('api.files.index');
Route::post('/files', [ApiFileController::class, 'store'])->name('api.files.store');
Route::get('/files/{id}', [ApiFileController::class, 'show'])->name('api.files.show');
Route::delete('/files/{id}', [ApiFileController::class, 'destroy'])->name('api.files.destroy');
Route::get('/files/view/{id}', [ApiFileController::class, 'view'])->name('api.files.view');
Route::middleware([EnsureFrontendRequestsAreStateful::class, 'auth:sanctum',])->get('/statistics', [ApiFileController::class, 'statistics'])->name('api.statistics');
Route::post('/logout', [ApiAuthController::class, 'logout'])->name('api.logout');
Route::get('/user', function (Request $request) {
return response()->json($request->user());
})->name('api.user');
});

