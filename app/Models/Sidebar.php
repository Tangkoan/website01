<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Sidebar extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'sidebars';
    
    protected $guarded = []; // ✅ Guarded ត្រឹមត្រូវ

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function parent()
    {
        return $this->belongsTo(Sidebar::class, 'parent_id');
    }


    // ✅ ១. Relationship សម្រាប់ទាញយកកូនៗ (Sub-menus)
    public function children()
    {
        return $this->hasMany(Sidebar::class, 'parent_id')->orderBy('order', 'asc');
    }
    
    
}