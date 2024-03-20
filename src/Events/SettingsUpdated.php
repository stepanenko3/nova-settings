<?php

namespace Stepanenko3\NovaSettings\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Stepanenko3\NovaSettings\Models\Settings;

class SettingsUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Settings $model,
    ) {
        //
    }
}
