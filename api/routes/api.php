    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\GameController;

    // Rotas da API
    Route::group(['middleware' => 'api','prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Rotas do Jogo
    Route::middleware('auth:api')->group(function () {
        Route::post('/game/{roomId}/start', [GameController::class, 'startGame']);
        Route::get('/game/{roomId}/state', [GameController::class, 'getState']);
    });