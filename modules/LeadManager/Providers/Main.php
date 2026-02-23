<?php

namespace Modules\LeadManager\Providers;

use Illuminate\Support\ServiceProvider;

class Main extends ServiceProvider
{
    public function register(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'lead-manager');
    }
}
