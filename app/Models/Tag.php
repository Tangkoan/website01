<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions; // 1. Import LogOptions

class Tag extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = ['name']; // your fillable fields...

    // 2. Add this required method
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name']) // Replace with the actual columns you want to log
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
            
        // Alternatively, if you just want to log all fillable attributes:
        // return LogOptions::defaults()->logFillable();
    }
}