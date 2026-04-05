<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * RouteServiceProvider
 * 
 * In Laravel 12, routing is configured in bootstrap/app.php
 * This provider is kept for backward compatibility but routes are now
 * managed in bootstrap/app.php
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = 'admin/home';
    public const SITE = '/';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rate limiting is now configured in bootstrap/app.php
        // Routes are configured in bootstrap/app.php
    }
}
