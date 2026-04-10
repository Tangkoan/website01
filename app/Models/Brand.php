<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Brand extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'brands';
    
    protected $guarded = []; // ✅ Guarded ត្រឹមត្រូវ

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the parent brand.
     */
    public function parent()
    {
        return $this->belongsTo(Brand::class, 'parent_id');
    }

    /**
     * Get the child brand.
     */
    public function children()
    {
        return $this->hasMany(Brand::class, 'parent_id');
    }
    
    
    protected $casts = [
        'images' => 'array'
    ];
}