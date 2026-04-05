<?php

namespace App\Providers;

use App\Models\Media;
use App\Models\GeneralSetting;
use App\Services\Core\BaseService;
use Illuminate\Pagination\Paginator;
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
        View::share('site_name', ['ar' => GeneralSetting::getValueForKey('website_name_ar'), 'en' => GeneralSetting::getValueForKey('website_name_en')]);
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
        Model::preventLazyLoading(!app()->isProduction());
    }
}
