<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Story;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Str;

class StoryPlatformSeeder extends Seeder
{
    public function run(): void
    {
        // ១. បង្កើត User គំរូមួយសិន (បើអ្នកមាន User ស្រាប់ វានឹងទាញយក User ទី១)
        $user = User::firstOrCreate(
            ['email' => 'admin@story.com'],
            ['name' => 'Super Admin', 'password' => bcrypt('password')]
        );

        // ២. បញ្ចូលទិន្នន័យ Categories ដូចក្នុងវីដេអូរបស់អ្នក
        $categories = [
            ['name' => 'Read With Us', 'slug' => 'read-with-us', 'is_active' => true],
            ['name' => 'News Healthy', 'slug' => 'news-healthy', 'is_active' => true],
            ['name' => 'Real Story', 'slug' => 'real-story', 'is_active' => true],
        ];
        
        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // ៣. បញ្ចូលទិន្នន័យ Tags ដែលពេញនិយមសម្រាប់អាមេរិក
        $tags = [
            ['name' => 'Trending', 'slug' => 'trending'],
            ['name' => 'Family Drama', 'slug' => 'family-drama'],
            ['name' => 'Revenge', 'slug' => 'revenge'],
            ['name' => 'Life Lesson', 'slug' => 'life-lesson'],
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(['slug' => $tag['slug']], $tag);
        }

        // ៤. បញ្ចូលទិន្នន័យ Story គំរូ ហើយភ្ជាប់ជាមួយ Tags (Pivot Table `story_tag`)
        $category = Category::where('slug', 'real-story')->first();
        
        if ($category) {
            $story = Story::updateOrCreate(
                ['slug' => 'my-husband-hid-his-wealth'],
                [
                    'category_id' => $category->id,
                    'user_id' => $user->id,
                    'title' => 'My husband hid his wealth from me for 10 years, so I took everything.',
                    'thumbnail' => 'https://via.placeholder.com/600x400.png?text=Read+The+Rest+Here',
                    'content' => '<p>This is a sample story content for testing the rich text editor and frontend display.</p>',
                    'meta_title' => 'Husband hides wealth - True Story',
                    'meta_description' => 'A shocking true story about a wife discovering her husbands secret fortune.',
                    'status' => 'published',
                    'published_at' => now(),
                ]
            );

            // ភ្ជាប់ Tag ទី១ និងទី២ ទៅសាច់រឿងនេះ
            $tagIds = Tag::take(2)->pluck('id');
            $story->tags()->sync($tagIds);
        }

        // ៥. បញ្ចូលទិន្នន័យ Settings (កូដ Ads)
        $settings = [
            ['key' => 'site_name', 'value' => 'Life Reader Stories'],
            ['key' => 'adsense_header', 'value' => '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>'],
            ['key' => 'adskeeper_footer', 'value' => '<div id="mgid-ad-widget"></div>'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}