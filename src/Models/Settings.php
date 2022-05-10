<?php
namespace Stepanenko3\NovaSettings\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
