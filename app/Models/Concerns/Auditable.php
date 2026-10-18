<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * Records created, updated and deleted events for the model in the activity log.
 */
trait Auditable
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logExcept(['password', 'remember_token', 'settings'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName(Str::snake(Str::pluralStudly(class_basename($this))))
            ->setDescriptionForEvent(
                fn (string $event): string => class_basename($this)." \"{$this->name}\" {$event}"
            );
    }
}
