<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for admin access
        Gate::define('admin', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-products', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-orders', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-coupons', function ($user) {
            return $user->hasRole('admin');
        });
    }
}
