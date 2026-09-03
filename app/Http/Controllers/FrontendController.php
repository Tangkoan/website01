<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\Tag;
use App\Models\Category;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index()
    {
        // កែមកប្រើ is_active សម្រាប់ Category
        $categories = Category::where('is_active', 1)->get();

        $stories = Story::with(['category', 'tags'])
                    ->where('status', 1) 
                    ->latest()
                    ->paginate(10); 

        $sidebarTags = Tag::latest()->take(15)->get();

        return view('frontend.home', compact('stories', 'sidebarTags', 'categories'));
    }

    // Function ថ្មីសម្រាប់ចាប់យកអត្ថបទតាម Category Slug
    public function category($slug)
    {
        // កុំភ្លេចលុបកូដ dd() ចោល

        $categories = Category::where('is_active', 1)->get();
        
        $currentCategory = Category::where('slug', $slug)->where('is_active', 1)->firstOrFail();

        // ប្រើ whereHas ដើម្បីចាប់យកអត្ថបទណាដែលមាន Category ត្រូវនឹង slug
        $stories = Story::with(['category', 'tags'])
                    ->whereHas('category', function ($query) use ($slug) {
                        $query->where('slug', $slug);
                    })
                    ->where('status', 1) 
                    ->latest()
                    ->paginate(10); 

        $sidebarTags = Tag::latest()->take(15)->get();

        return view('frontend.category', compact('stories', 'sidebarTags', 'currentCategory'));
    }

    public function show($slug)
    {
        $categories = Category::where('is_active', 1)->get();

        $story = Story::with(['category', 'tags'])->where('slug', $slug)->firstOrFail();

        $recentPosts = Story::with('category')
                        ->where('status', 1)
                        ->where('id', '!=', $story->id)
                        ->latest()
                        ->take(5)
                        ->get();

        $sidebarTags = Tag::latest()->take(15)->get();

        return view('frontend.story-detail', compact('story', 'recentPosts', 'sidebarTags', 'categories'));
    }
}