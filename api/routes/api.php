    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\GameController;

    Route::group(['middleware' => 'api','prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('{roomId}/start', [GameController::class, 'startGame']);
    });

