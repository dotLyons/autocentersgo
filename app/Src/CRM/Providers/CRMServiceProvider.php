<?php

namespace App\Src\CRM\Providers;

use Illuminate\Support\ServiceProvider;

class CRMServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register any CRM specific bindings here
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
    }
}
