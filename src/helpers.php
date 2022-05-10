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
    function settings(string|null $section, string|null $key = null, string|null $default = null, string|null $env = null)
    {
        if (!$env) $env = config('app.env');

        $settings = Cache::remember('settings.' . $env, config('cache.lifetime'), function() use ($section, $env) {
            return config('nova-settings.model')::query()
                ->select('settings')
                ->where('slug', $section)
                ->where('env', $env)
                ->first()
                ->settings ?? [];
        });

        return $key === null
            ? $settings
            : $settings[$key] ?? $default ?? null;
    }
}
