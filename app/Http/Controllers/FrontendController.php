<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\Tag;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index()
    {
        // ទាញយក Stories ភ្ជាប់ជាមួយ Category និង Tags យកតែអត្ថបទដែល Active (status = 1)
        $stories = Story::with(['category', 'tags'])
                    ->where('status', 1) 
                    ->latest()
                    ->paginate(10); 

        // ទាញយក Tags ទាំងអស់សម្រាប់បង្ហាញនៅ Sidebar
        $sidebarTags = Tag::latest()->take(15)->get();

        return view('frontend.home', compact('stories', 'sidebarTags'));
    }

    public function show($slug)
    {
        // ទាញទិន្នន័យ Detail ភ្ជាប់ជាមួយ Category និង Tags
        $story = Story::with(['category', 'tags'])->where('slug', $slug)->firstOrFail();

        // ទាញអត្ថបទថ្មីៗសម្រាប់ Sidebar (មិនបាច់យកអត្ថបទកំពុងអានទេ)
        $recentPosts = Story::with('category')
                        ->where('status', 1)
                        ->where('id', '!=', $story->id)
                        ->latest()
                        ->take(5)
                        ->get();

        // ទាញយក Tags សម្រាប់ Sidebar
        $sidebarTags = Tag::latest()->take(15)->get();

        return view('frontend.story-detail', compact('story', 'recentPosts', 'sidebarTags'));
    }
}