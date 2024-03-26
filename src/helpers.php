<?php

use Illuminate\Support\Facades\Cache;

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

        $settings = Cache::remember(
            key: 'settings.' . $section . '.' . $env,
            ttl: config('cache.lifetime'),
            callback: fn () => config('nova-settings.model')::query()
                ->select('settings')
                ->where('slug', $section)
                ->where('env', $env)
                ->first()
                ->settings ?? [],
        );

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
