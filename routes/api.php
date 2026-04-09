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
        Route::post('logout', [LogoutController::class, '__invoke'])->middleware('auth:sanctum');
        Route::get('profile', [ProfileController::class, '__invoke'])->middleware('auth:sanctum');
        Route::patch('profile', [UpdateProfileController::class, '__invoke'])->middleware('auth:sanctum');
        Route::patch('password', [ChangePasswordController::class, '__invoke'])->middleware('auth:sanctum');
        Route::get('notification-settings', [NotificationSettingsController::class, 'show'])->middleware('auth:sanctum');
        Route::patch('notification-settings', [NotificationSettingsController::class, 'update'])->middleware('auth:sanctum');
    });
    Route::prefix('general')->group(function () {
        include __DIR__.'/guard/general.php';
    });

    Route::middleware('auth:sanctum')->prefix('company')->group(function () {
        Route::get('opportunities', [CompanyOpportunityController::class, 'index']);
        Route::post('opportunities', [CompanyOpportunityController::class, 'store']);
        Route::get('opportunities/{opportunity}', [CompanyOpportunityController::class, 'show']);
        Route::put('opportunities/{opportunity}', [CompanyOpportunityController::class, 'update']);
    });
});
