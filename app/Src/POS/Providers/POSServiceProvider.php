<?php

namespace App\Src\POS\Providers;

use Illuminate\Support\ServiceProvider;

class POSServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register any POS specific bindings here
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
    }
}
