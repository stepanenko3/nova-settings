<?php

namespace Stepanenko3\NovaSettings;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Tool;
use Stepanenko3\NovaSettings\Resources\Settings;

class NovaSettingsTool extends Tool
{
    public function boot(): void
    {
        //
    }

    public function menu(
        Request $request,
    ) {
        return MenuSection::resource(Settings::class)
            ->icon('cog');
    }
}
