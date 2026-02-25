<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(\App\Src\CRM\Providers\CRMServiceProvider::class);
        $this->app->register(\App\Src\POS\Providers\POSServiceProvider::class);
        $this->app->register(\App\Src\Vehicles\Providers\VehiclesServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
