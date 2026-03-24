<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Role extends SpatieRole
{
    use SoftDeletes, LogsActivity;

    protected $fillable = ['name', 'guard_name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'guard_name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            // បន្ថែមបន្ទាត់ខាងក្រោមនេះ៖ កុំឱ្យវា Log ជា 'updated' ប្រសិនបើប្តូរតែ deleted_at (ពេល Restore)
            ->dontLogIfAttributesChangedOnly(['deleted_at']) 
            ->useLogName('role');
    }

    public function tapActivity(\Spatie\Activitylog\Models\Activity $activity, string $eventName)
    {
        // កំណត់ឱ្យ description ទៅតាម Event (created, updated, deleted, restored)
        $activity->description = $eventName;
    }
}