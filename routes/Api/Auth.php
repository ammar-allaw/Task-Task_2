    <?php

    use App\Http\Controllers\API\AuthController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;

    Route::controller(AuthController::class)
    ->prefix('auth')->group(function(){
        Route::middleware('guest:sanctum')->group(function(){
            Route::post('Signup','signup')->name('Auth.Signup');
            Route::post('Login','login')->name('Auth.Logout');
        });
        Route::middleware('auth:sanctum')->group(function(){
                Route::post('VerifiedEmail','verifiedEmail')->name('Auth.Login');
                Route::post('RefreshToken','refreshToken')->name('Auth.RefreshToken');
                Route::post('Logout','logout')->name('Auth.Logout');


            });
    });