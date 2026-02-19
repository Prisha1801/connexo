<?php

namespace Modules\LeadBot\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as Provider;

class Main extends Provider
{
    public function register()
    {
        $this->loadConfig();
        $this->loadRoutes();
    }

    public function boot()
    {
        $this->publishConfig();
        $this->loadViews();
        $this->loadViewComponents();
        $this->loadTranslations();
        $this->loadMigrations();
    }

    protected function loadConfig()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'lead-bot'
        );
    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('lead-bot.php'),
        ], 'config');
    }

    public function loadViews()
    {
        $viewPath = resource_path('views/modules/lead-bot');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/lead-bot';
        }, \Config::get('view.paths')), [$sourcePath]), 'lead-bot');
    }

    public function loadViewComponents()
    {
        Blade::componentNamespace('Modules\LeadBot\View\Components', 'lead-bot');
    }

    public function loadTranslations()
    {
        $langPath = resource_path('lang/modules/lead-bot');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'lead-bot');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang/en', 'lead-bot');
        }
    }

    public function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    public function loadRoutes()
    {
        if (app()->routesAreCached()) {
            return;
        }

        $routes = [
            'web.php',
            'api.php',
        ];

        foreach ($routes as $route) {
            $this->loadRoutesFrom(__DIR__ . '/../Routes/' . $route);
        }
    }

    public function provides()
    {
        return [];
    }
}

