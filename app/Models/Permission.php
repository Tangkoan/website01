<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Permission extends SpatiePermission
{
    use SoftDeletes, LogsActivity;

    // កំណត់ការកត់ត្រា Audit Trail (Activity Log)
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'guard_name']) // តាមដាន Column ទាំងនេះ
            ->logOnlyDirty() // កត់ត្រាតែពេលមានទិន្នន័យផ្លាស់ប្ដូរ
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "This permission has been {$eventName}");
    }
}