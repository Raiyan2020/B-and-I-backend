<?php


use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\ChangePasswordController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\NotificationSettingsController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\RegisterAdvertiserController;
use App\Http\Controllers\Api\V1\Auth\RegisterInvestorController;
use App\Http\Controllers\Api\V1\Auth\ResendVerificationController;
use App\Http\Controllers\Api\V1\Auth\UpdateProfileController;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use App\Http\Controllers\Api\V1\Company\OpportunityController as CompanyOpportunityController;
use App\Http\Controllers\Api\V1\General\CategoryController;
use App\Http\Controllers\Api\V1\General\ReferenceDataController;
use App\Http\Controllers\Api\V1\General\HomeController;
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
    Route::prefix('auth')->group(function () {
        Route::post('register/investor', [RegisterInvestorController::class, '__invoke']);
        Route::post('register/advertiser', [RegisterAdvertiserController::class, '__invoke']);

        Route::post('login', [LoginController::class, '__invoke']);

        Route::post('email/resend', [ResendVerificationController::class, '__invoke'])->middleware('throttle:6,1');
        Route::get('email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->name('api.v1.auth.verification.verify');
    });
    Route::prefix('general')->group(function () {
        include __DIR__ . '/guard/general.php';
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('auth')->group(function () {
            Route::post('logout', [LogoutController::class, '__invoke']);

            Route::get('profile', [ProfileController::class, '__invoke']);
            Route::patch('profile', [UpdateProfileController::class, '__invoke']);
            Route::patch('password', [ChangePasswordController::class, '__invoke']);
            Route::get('notification-settings', [NotificationSettingsController::class, 'show']);
            Route::patch('notification-settings', [NotificationSettingsController::class, 'update']);
        });

        Route::group(['prefix' => 'company/opportunities', 'controller' => CompanyOpportunityController::class], function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{opportunity}', 'show');
            Route::put('/{opportunity}', 'update');
        });
    });
});
