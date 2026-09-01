<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Story extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'stories';
    
    protected $guarded = []; // ✅ Guarded ត្រឹមត្រូវ

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    // ១. ទំនាក់ទំនងទៅកាន់ Category (សាច់រឿងនេះជារបស់ Category មួយណា)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // ២. ទំនាក់ទំនងទៅកាន់ User (អ្នកណាជាអ្នក Post សាច់រឿងនេះ)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ៣. ទំនាក់ទំនងទៅកាន់ Tag (សាច់រឿងនេះមាន Tag អ្វីខ្លះ)
    public function tags()
    {
        // ដោយសារ Pivot Table យើងឈ្មោះ story_tag យើងបញ្ជាក់វាឲ្យច្បាស់តែម្តង
        return $this->belongsToMany(Tag::class, 'story_tag', 'story_id', 'tag_id');
    }
    
}