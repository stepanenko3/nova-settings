<?php

use Illuminate\Support\Facades\Cache;

if (!function_exists('settings')) {
    /**
     * settings
     *
     * @param  string|null $section
     * @param  string|null $key
     * @param  string|null $default
     * @param  string|null $env
     * @return string
     */
    function settings(
        string|null $section,
        string|null $key = null,
        string|null $default = null,
        string|null $env = null,
    ) {
        if (!$env) $env = config('app.env');

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
