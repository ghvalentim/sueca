    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AuthController;

    Route::group(['middleware' => 'api','prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::get('me', [AuthController::class, 'me']);
    });
