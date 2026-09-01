<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StoryTag extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'story_tag';
    
    protected $guarded = []; // ✅ Guarded ត្រឹមត្រូវ

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id');
    }

    // បន្ថែមមុខងារទំនាក់ទំនងទី ២៖ ភ្ជាប់ទៅ Model Tag
    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }
    
}