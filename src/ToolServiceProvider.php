<?php

namespace Stepanenko3\NovaSettings;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Event;
use Stepanenko3\NovaSettings\Events\SettingsUpdated;
use Stepanenko3\NovaSettings\Events\SettingsDeleted;
use Illuminate\Support\Facades\Cache;

class ToolServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->config();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish data
        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->app->booted(function (): void {
            Nova::resources([
                config('nova-settings.resource'),
            ]);
        });

        Event::listen(
            [
                SettingsUpdated::class,
                SettingsDeleted::class,
            ],
            function (SettingsUpdated | SettingsDeleted $event): void {
                Cache::forget('settings.' . $event->model->slug . '.' . $event->model->env);
            },
        );
    }

    private function config(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__ . '/../config/' => config_path(),
            ], 'config');
        }

        $this->mergeConfigFrom(
            __DIR__ . '/../config/nova-settings.php',
            'nova-settings'
        );
    }
}
