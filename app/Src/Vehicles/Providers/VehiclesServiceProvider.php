<?php

namespace App\Src\Vehicles\Providers;

use Illuminate\Support\ServiceProvider;

class VehiclesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register bindings
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
    }
}
