<?php


use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\UserPasswordController;
use App\Http\Controllers\Api\V1\Auth\ChangeEmailController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\NotificationSettingsController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\UserRegisterController;
use App\Http\Controllers\Api\V1\Auth\ResendVerificationController;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use App\Http\Controllers\Api\V1\Company\OpportunityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->middleware('set.locale.from.header')->group(function () {

    Route::prefix('general')->group(function () {
        include __DIR__ . '/guard/general.php';
    });

    Route::prefix('auth')->group(function () {

        Route::group(['prefix' => 'register', 'controller' => UserRegisterController::class], function () {
            Route::post('/investor', 'investorRegister');
            Route::post('/advertiser', 'advertiserRegister');
        });

        Route::post('login', [LoginController::class, '__invoke']);

        Route::post('resend-code', [ResendVerificationController::class, '__invoke'])->middleware('throttle:6,1');
        Route::post('verify-code', [VerifyEmailController::class, '__invoke']);
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('auth')->group(function () {
            Route::post('logout', [LogoutController::class, '__invoke']);

            Route::group(['prefix' => 'profile', 'controller' => ProfileController::class], function () {
                Route::get('/', '__invoke');
                Route::patch('/', 'update');
            });

            Route::group(['prefix' => 'email-change', 'controller' => ChangeEmailController::class], function () {
                Route::post('request-current', 'requestCurrent')->middleware('throttle:6,1');
                Route::post('verify-current', 'verifyCurrent');
                Route::post('request-new', 'requestNew')->middleware('throttle:6,1');
                Route::post('verify-new', 'verifyNew');
            });

            Route::patch('password', [UserPasswordController::class, 'changePassword']);

            Route::group(['prefix' => 'notification-settings', 'controller' => NotificationSettingsController::class], function () {
                Route::get('/', 'show');
                Route::patch('/', 'update');
            });
        });

        Route::group(['prefix' => 'company/opportunities', 'controller' => OpportunityController::class], function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{opportunity}', 'show');
            Route::put('/{opportunity}', 'update');
        });
    });
});
