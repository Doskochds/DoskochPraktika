use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\OneTimeLinkController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware([EnsureFrontendRequestsAreStateful::class, 'auth:sanctum'])->group(function () {

    Route::get('/files', [FileController::class, 'index']);
    Route::post('/files', [FileController::class, 'store']);
    Route::get('/files/{id}', [FileController::class, 'show']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);
    Route::get('/files/view/{id}', [FileController::class, 'view']);
    Route::get('/files/statistics', [FileController::class, 'statistics']);

    Route::post('/files/{id}/generate-links', [OneTimeLinkController::class, 'generate']);
    Route::get('/links/{token}', [OneTimeLinkController::class, 'view']);
    Route::delete('/links/{token}', [OneTimeLinkController::class, 'deleteLink']);
    Route::delete('/links/cleanup', [OneTimeLinkController::class, 'cleanUpLinks']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
