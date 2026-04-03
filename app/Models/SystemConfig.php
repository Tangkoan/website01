<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity; // <- បន្ថែមបន្ទាត់នេះ
use Spatie\Activitylog\LogOptions;          // <- បន្ថែមបន្ទាត់នេះ

class SystemConfig extends Model
{
    use LogsActivity; // <- ប្រាប់ឱ្យវាចាប់ផ្តើមប្រើមុខងារ Activity Log

    protected $guarded = [];

    // បម្លែង options ពី JSON ទៅ Array ដោយស្វ័យប្រវត្តិ
    protected $casts = [
        'options' => 'array', 
    ];

    // ប្រាប់ឱ្យប្រព័ន្ធកត់ត្រារាល់សកម្មភាពទាំងអស់ (Create, Update, Delete)
    protected static $recordEvents = ['created', 'updated', 'deleted'];

    // កំណត់ការកត់ត្រា (Audit Trail / Activity Log)
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // តាមដាន Column ទាំងអស់នេះ ពេលមានអ្នកកែប្រែ
            ->logOnly(['group', 'name', 'key', 'type', 'value', 'options']) 
            
            // កត់ត្រាតែនៅពេលដែលទិន្នន័យមានការផ្លាស់ប្តូរពិតប្រាកដប៉ុណ្ណោះ (សន្សំសំចៃទំហំ Database)
            ->logOnlyDirty() 
            
            // កុំកត់ត្រាប្រសិនបើគ្មានអ្វីផ្លាស់ប្តូរទាល់តែសោះ
            ->dontSubmitEmptyLogs()
            
            // សារពណ៌នាអំពីសកម្មភាព (ឧ. This System Config has been updated)
            ->setDescriptionForEvent(fn(string $eventName) => "This System Config has been {$eventName}");
    }
}