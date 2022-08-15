<?php

namespace Stepanenko3\NovaSettings\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Stepanenko3\NovaSettings\Models\Settings;

class SettingsDeleted
{
    use Dispatchable, SerializesModels;

    public $model;

    public function __construct(Settings $model)
    {
        $this->model = $model;
    }
}
