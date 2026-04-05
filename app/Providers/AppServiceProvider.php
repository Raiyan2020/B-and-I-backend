<?php

namespace App\Providers;

use App\Models\Media;
use App\Models\GeneralSetting;
use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Core\BaseService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application vendorSetting.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AuthServiceInterface::class, AuthService::class);

        $this->app->singleton('base-service', function ($app, $parameters = []) {
            $model = $parameters['model'] ?? null;
            return new BaseService($model);
        });
    }

    /**
     * Bootstrap any application vendorSetting.
     *
     * @return void
     */
    public function boot()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        View::share('site_name', ['ar' => GeneralSetting::getValueForKey('website_name_ar'), 'en' => GeneralSetting::getValueForKey('website_name_en')]);
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
        Model::preventLazyLoading(!app()->isProduction());
    }
}
