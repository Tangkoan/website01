<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'user_id', 'title', 'slug', 'thumbnail', 
        'content', 'views_count', 'status', 'published_at'
    ];

    // ទំនាក់ទំនង: សាច់រឿងនេះជារបស់ Category ណា?
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // ទំនាក់ទំនង: អ្នកណាជាអ្នក Post រឿងនេះ?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ទំនាក់ទំនង: រឿងនេះមាន Tag អ្វីខ្លះ? (Many-to-Many)
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}