<?php

namespace Stepanenko3\NovaSettings;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Event;
use Stepanenko3\NovaSettings\Events\SettingsUpdated;
use Stepanenko3\NovaSettings\Events\SettingsDeleted;
use Illuminate\Support\Facades\Cache;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->config();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish data
        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->app->booted(function () {
            config('nova-settings.model')::saving(function ($model) {
                //
            });

            Nova::resources([
                config('nova-settings.resource'),
            ]);
        });

        Nova::serving(function (ServingNova $event) {
            //
        });

        Event::listen(
            [
                SettingsUpdated::class,
                SettingsDeleted::class,
            ],
            function (SettingsUpdated $event) {
                Cache::forget('settings.' . $event->model->slug . '.' . $event->model->env);
            },
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    private function config()
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
