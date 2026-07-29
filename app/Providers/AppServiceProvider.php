<?php

namespace App\Providers;

use App\Models\UserRole;
use App\Support\GeoLocationResolver;
use App\Support\IpApiResolver;
use App\Support\NullGeoResolver;
// use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GeoLocationResolver::class, function (): GeoLocationResolver {
            if (! config('activity-log.geo.enabled')) {
                return new NullGeoResolver;
            }

            return match (config('activity-log.geo.driver')) {
                'ip-api' => new IpApiResolver,
                default => new NullGeoResolver,
            };
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Paginator::useBootstrap();

        // Prevent database queries during migrations/tests
        if (! app()->runningInConsole()) {
            $roles = UserRole::all();
            View::share('roles', $roles);
        }
    }
}
