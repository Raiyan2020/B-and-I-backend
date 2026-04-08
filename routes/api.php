<?php


use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\RegisterAdvertiserController;
use App\Http\Controllers\Api\V1\Auth\RegisterInvestorController;
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
        Route::post('logout', [LogoutController::class, '__invoke'])->middleware('auth:sanctum');
        Route::get('profile', [ProfileController::class, '__invoke'])->middleware('auth:sanctum');
    });
    Route::prefix('general')->group(function () {
        include_once __DIR__.'/guard/general.php';
    });
});
