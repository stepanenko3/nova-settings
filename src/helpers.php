<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

if (!function_exists('settings')) {
    function settings(
        ?string $section,
        ?string $key = null,
        ?string $default = null,
        ?string $env = null,
    ): mixed {
        if (!$env) {
            $env = config('app.env');
        }

        $configKey = 'nova-settings.storage.' . $section . '.' . $env;

        if ($value = config($configKey)) {
            $settings = json_decode($value, true);
        } else {
            $settings = Cache::remember(
                key: 'settings.' . $section . '.' . $env,
                ttl: config('nova-settings.cache_lifetime', 3600),
                callback: fn () => config('nova-settings.model')::query()
                    ->select('settings')
                    ->where('slug', $section)
                    ->where('env', $env)
                    ->first()
                    ->settings ?? [],
            );

            Config::set(
                key: $configKey,
                value: json_encode($settings),
            );
        }

        if ($key === null) {
            return $settings;
        }

        $key = str_ireplace(
            search: '->',
            replace: '.',
            subject: $key,
        );

        return data_get(
            target: $settings,
            key: $key,
            default: $default,
        );
    }
}
