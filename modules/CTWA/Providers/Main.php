<?php

namespace Modules\CTWA\Providers;

use Illuminate\Support\ServiceProvider;

class Main extends ServiceProvider
{
    public function register(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'ctwa');
    }
}
