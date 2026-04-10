<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Sidebar extends Model
{
    // ✅ បំពាក់អាវក្រោះទាំង ៣៖ Factory, Trash (SoftDeletes), និង Activity Log
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'sidebars';
    
    // ✅ អនុញ្ញាតឱ្យ Insert ទិន្នន័យបានគ្រប់ Field
    protected $guarded = [];

    /**
     * កំណត់រចនាសម្ព័ន្ធសម្រាប់ Activity Log 
     * (កត់ត្រាតែអ្វីដែលផ្លាស់ប្តូរ និងមិនកត់ត្រា Log ទទេ)
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    /**
     * ទាញយក Menu មេ (Parent)
     */
    public function parent()
    {
        return $this->belongsTo(Sidebar::class, 'parent_id');
    }

    /**
     * ទាញយក Menu កូនៗ (Children) ព្រមទាំងតម្រៀបតាមលេខរៀង (order)
     */
    public function children()
    {
        return $this->hasMany(Sidebar::class, 'parent_id')->orderBy('order', 'asc');
    }
}