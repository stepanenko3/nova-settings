<?php
namespace Stepanenko3\NovaSettings\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Stepanenko3\NovaSettings\Events\SettingsDeleted;
use Stepanenko3\NovaSettings\Events\SettingsUpdated;

class Settings extends Model
{
    use LogsActivity;

    protected $casts = [
        'fields' => 'array',
        'settings' => 'array',
    ];

    protected $fillable = [
        'slug',
        'env',
        'type',
        'fields',
        'settings',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => SettingsUpdated::class,
        'deleted' => SettingsDeleted::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
