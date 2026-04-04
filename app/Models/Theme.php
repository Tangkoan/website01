<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    //
    protected $fillable = ['name', 'colors', 'is_active'];

    // protected $casts = [
    //     'colors' => 'array', // បម្លែង JSON ទៅជា Array ងាយស្រួលប្រើ
    // ];

    // នៅក្នុង App\Models\Theme.php
    protected $casts = [
        'colors' => 'array',
        'is_active' => 'boolean'
    ];
}
