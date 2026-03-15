<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    //
    protected $fillable = ['name', 'colors', 'is_active'];

    protected $casts = [
        'colors' => 'array', // បម្លែង JSON ទៅជា Array ងាយស្រួលប្រើ
    ];
}
