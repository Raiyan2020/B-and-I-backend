<?php

namespace App\Providers;

use App\Models\Opportunity;
use App\Policies\AdPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Opportunity::class => AdPolicy::class,
    ];

    /**
     * Register any authentication / authorization vendorSetting.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
