<?php


use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\PreferredSectorController;
use App\Http\Controllers\Dashboard\AboutUsItemController;
use App\Http\Controllers\Dashboard\FeatureController;
use App\Http\Controllers\Dashboard\SubscriptionPackageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\RolesController;
use App\Http\Controllers\Dashboard\NotificationsController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Dashboard\GeneralSettingController;
use App\Http\Controllers\Dashboard\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('admin.login');
});
Route::group([
    'prefix' => LaravelLocalization::setLocale() . '/admin',
    'as' => 'admin.'
], function () {
    Route::group(['controller' => AuthController::class, 'middleware' => 'guest:admin'], function () {
        Route::get('', 'index')->name('login')->middleware('guest');
        Route::post('', 'login')->name('startSession');
    });

    Route::group(['middleware' => 'auth:admin'], function () {
        Route::get('home', [HomeController::class, 'index'])->name('home');
        Route::get('destroy', [AuthController::class, 'logout'])->name('logout');

        Route::group(['prefix' => 'profile', 'controller' => AuthController::class], function () {
            Route::get('', 'profile')->name('profile');
            Route::put('update', 'update_profile')->name('profile.update');
        });

        Route::group(['prefix' => 'general_settings', 'controller' => GeneralSettingController::class], function () {
            Route::get('manage', 'generalSettings')->name('generalSetting.index');
            Route::post('store', 'store')->name('generalSetting.store');
            Route::patch('terms', 'updateTerms')->name('generalSetting.terms.update');
            Route::patch('privacy', 'updatePrivacy')->name('generalSetting.privacy.update');
        });

        // Admin specific routes (must be before Route::resources to avoid route conflict)
        Route::controller(AdminController::class)->group(function () {
            Route::post('admins/destroy-multiple', 'destroyMultiple')->name('admins.destroyMultiple');
            Route::get('admins/{admin}/toggle-block', 'toggleBlock')->name('admins.toggleBlock');
        });

        // User specific routes (must be before Route::resources to avoid route conflict)
        Route::controller(UserController::class)->group(function () {
            Route::post('users/destroy-multiple', 'destroyMultiple')->name('users.destroyMultiple');
            Route::get('users/{user}/toggle-block', 'toggleBlock')->name('users.toggleBlock');
            Route::get('users/{user}/toggle-active', 'toggleActive')->name('users.toggleActive');
            Route::post('users/{user}/send-notification', 'sendNotification')->name('users.sendNotification');
            Route::post('users/{user}/charge-wallet', 'chargeWallet')->name('users.chargeWallet');
        });

        // Category specific routes (must be before Route::resources to avoid route conflict)
        Route::controller(CategoryController::class)->group(function () {
            Route::post('categories/destroy-multiple', 'destroyMultiple')->name('categories.destroyMultiple');
            Route::get('categories/{category}/toggle-status', 'toggleStatus')->name('categories.toggleStatus');
        });

        Route::controller(PreferredSectorController::class)->group(function () {
            Route::post('preferred-sectors/destroy-multiple', 'destroyMultiple')->name('preferred_sectors.destroyMultiple');
            Route::get('preferred-sectors/{preferred_sector}/toggle-status', 'toggleStatus')->name('preferred_sectors.toggleStatus');
        });

        // About Us Items routes
        Route::controller(AboutUsItemController::class)->group(function () {
            Route::post('about-us-items/update-settings', 'updateSettings')->name('about_us_items.updateSettings');
            Route::post('about-us-items/destroy-multiple', 'destroyMultiple')->name('about_us_items.destroyMultiple');
            Route::get('about-us-items/{about_us_item}/toggle-status', 'toggleStatus')->name('about_us_items.toggleStatus');
        });

        // Features routes
        Route::controller(FeatureController::class)->group(function () {
            Route::post('features/destroy-multiple', 'destroyMultiple')->name('features.destroyMultiple');
            Route::get('features/{feature}/toggle-status', 'toggleStatus')->name('features.toggleStatus');
        });

        Route::controller(SubscriptionPackageController::class)->group(function () {
            Route::post('subscription-packages/update-settings', 'updateSettings')->name('subscription_packages.updateSettings');
            Route::post('subscription-packages/destroy-multiple', 'destroyMultiple')->name('subscription_packages.destroyMultiple');
            Route::get('subscription-packages/{subscription_package}/toggle-status', 'toggleStatus')->name('subscription_packages.toggleStatus');
        });

        Route::resources([
            'admins' => AdminController::class,
            'roles' => RolesController::class,
            'categories' => CategoryController::class,
            'preferred_sectors' => PreferredSectorController::class,
            'about_us_items' => AboutUsItemController::class,
            'features' => FeatureController::class,
            'subscription_packages' => SubscriptionPackageController::class,
            'users' => UserController::class,
        ]);

        // Bulk delete routes (destroyAll) - Legacy routes for backward compatibility
        Route::post('roles/destroyAll', [RolesController::class, 'destroyAll'])->name('roles.destroyAll');

        Route::get('notifications/read-all', [NotificationsController::class, 'readAll'])->name('notifications.read_all');
        Route::get('notifications/{notification}/read', [NotificationsController::class, 'read'])->name('notifications.read');
        Route::patch('/fcm-token', [NotificationsController::class, 'updateToken'])->name('fcmToken');
    });
});
