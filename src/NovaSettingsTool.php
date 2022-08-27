<?php

namespace Stepanenko3\NovaSettings;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Tool;
use Stepanenko3\NovaSettings\Resources\Settings;

class NovaSettingsTool extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     */
    public function boot()
    {
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function menu(Request $request)
    {
        return MenuItem::resource(Settings::class);
    }
}
