<?php


use App\Http\Controllers\Api\V1\Auth\AccountDeletionRequestController;
use App\Http\Controllers\Api\V1\Auth\ChangeEmailController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\NotificationController;
use App\Http\Controllers\Api\V1\Auth\NotificationSettingsController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\ProfileUpdateRequestController;
use App\Http\Controllers\Api\V1\Auth\ResendVerificationController;
use App\Http\Controllers\Api\V1\Auth\UserPasswordController;
use App\Http\Controllers\Api\V1\Auth\UserRegisterController;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use App\Http\Controllers\Api\V1\Company\InvestorInterestRequestController;
use App\Http\Controllers\Api\V1\Company\OpportunityController;
use App\Http\Controllers\Api\V1\General\MyFatoorahSessionController;
use App\Http\Controllers\Api\V1\General\OpportunityController as GeneralOpportunityController;
use App\Http\Controllers\Api\V1\Investor\OpportunityController as InvestorOpportunityController;
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

    // general routes - no auth required
    Route::prefix('general')->group(function () {
        include __DIR__ . '/guard/general.php';
    });

    Route::prefix('auth')->group(function () {

        Route::group(['prefix' => 'register', 'controller' => UserRegisterController::class], function () {
            Route::post('/investor', 'investorRegister');
            Route::post('/advertiser', 'advertiserRegister');
        });

        Route::post('login', [LoginController::class, '__invoke']);

        Route::group(['prefix' => 'password/forgot', 'controller' => UserPasswordController::class], function () {
            Route::post('request-code', 'requestForgotPasswordOtp')->middleware('throttle:6,1');
            Route::post('verify-code', 'verifyForgotPasswordOtp');
            Route::post('reset', 'resetForgottenPassword');
        });

        Route::post('resend-code', [ResendVerificationController::class, '__invoke'])->middleware('throttle:6,1');
        Route::post('verify-code', [VerifyEmailController::class, '__invoke']);
    });

    Route::middleware(['auth:sanctum', 'api.account.access'])->group(function () {

        Route::prefix('auth')->group(function () {
            Route::post('logout', [LogoutController::class, '__invoke']);

            Route::group(['prefix' => 'profile', 'controller' => ProfileController::class], function () {
                Route::get('/', '__invoke');
                Route::patch('/', 'update');
            });

            Route::get('profile-update-requests/latest', [ProfileUpdateRequestController::class, 'latest']);

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

            Route::group(['prefix' => 'notifications', 'controller' => NotificationController::class], function () {
                Route::get('/', 'index');
                Route::get('/unread-count', 'unreadCount');
                Route::patch('/read-all', 'markAllAsRead');
                Route::delete('/delete-all', 'destroyAll');
                Route::delete('/{notification}', 'destroy');
                Route::post('/{notification}/read', 'markAsRead');

            });

            Route::group(['prefix' => 'account-deletion-requests', 'controller' => AccountDeletionRequestController::class], function () {
                Route::get('latest', 'latest');
                Route::post('/', 'store');
            });
        });

        Route::group(['prefix' => 'company/opportunities', 'controller' => OpportunityController::class], function () {
            Route::get('/', 'index');
            Route::get('/purchased-seats', 'purchasedSeats');
            Route::get('/sent-interests', 'sentInterests');
            Route::get('/current-requests', 'currentRequests');
            Route::post('/', 'store');
            Route::get('/{opportunity}', 'show');
            Route::put('/{opportunity}', 'update');
        });

        Route::group(['prefix' => 'company/investor-interest-requests', 'controller' => InvestorInterestRequestController::class], function () {
            Route::post('/', 'store');
        });

        Route::group(['prefix' => 'investor/opportunities', 'controller' => InvestorOpportunityController::class], function () {
            Route::get('purchased-seats', 'purchasedSeats');
            Route::get('sent-interests', 'sentInterests');
            Route::get('current-requests', 'currentRequests');
        });

        Route::group(['prefix' => 'opportunities/{opportunity}', 'controller' => GeneralOpportunityController::class], function () {
            Route::post('seats',  'purchaseSeat');
            Route::post('interest-requests',  'submitInterest');
        });

        Route::post('create-my-fatoorah-session', [MyFatoorahSessionController::class, '__invoke']);
    });
});
