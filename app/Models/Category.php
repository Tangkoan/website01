<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // ✅ ថែម SoftDeletes
use Spatie\Activitylog\Traits\LogsActivity; // ✅ ថែម Activity Log
use Spatie\Activitylog\LogOptions;

class Category extends Model
{
    use HasFactory, SoftDeletes, LogsActivity; // ✅ ដាក់ឱ្យប្រើនៅទីនេះ

    protected $guarded = [];

    // ✅ កំណត់ឱ្យវាចេះកត់ត្រា Log ស្វ័យប្រវត្តិពេលមានការកែប្រែ
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable()
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
    }
}