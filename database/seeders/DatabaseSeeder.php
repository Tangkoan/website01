<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Theme;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // លុបទិន្នន័យចាស់ចោលសិន (បើមាន) ដើម្បីកុំឱ្យជាន់គ្នាពេលរត់ Command ម្ដងទៀត
        Theme::truncate();

        Theme::create([
            'name' => 'Default Blue Theme',
            'is_active' => true,
            'colors' => [
                'light' => [
                    'primary' => '#3b82f6',       // ពណ៌ខៀវ (Blue 500)
                    'primary_text' => '#ffffff',  // អក្សរសលើប៊ូតុង Primary
                    
                    'background' => '#f3f4f6',    // ពណ៌ប្រផេះស្រាលសម្រាប់ផ្ទៃទូទៅ (Gray 100)
                    'header' => '#ffffff',        // ពណ៌សសម្រាប់ Header
                    'sidebar' => '#ffffff',       // ពណ៌សសម្រាប់ Sidebar
                    'card_bg' => '#ffffff',       // ពណ៌សសម្រាប់ Cards
                    'dropdown' => '#ffffff',      // ពណ៌សសម្រាប់ Dropdown & Modals
                    
                    'border_color' => '#e5e7eb',  // ពណ៌ស៊ុម (Gray 200)
                    
                    'text_main' => '#1f2937',     // អក្សរពណ៌ខ្មៅស្រអាប់ (Gray 800)
                    'text_muted' => '#6b7280',    // អក្សរពណ៌ប្រផេះសម្រាប់ពិពណ៌នា (Gray 500)
                ],
                'dark' => [
                    'primary' => '#3b82f6',       // រក្សាពណ៌ខៀវដដែល ឬអាចប្ដូរជា #60a5fa ឱ្យភ្លឺជាងមុន
                    'primary_text' => '#ffffff',
                    
                    'background' => '#0f172a',    // ផ្ទៃខាងក្រោយងងឹតខ្លាំង (Slate 900)
                    'header' => '#1e293b',        // ពណ៌ Header ងងឹតល្មម (Slate 800)
                    'sidebar' => '#1e293b',       // ពណ៌ Sidebar ងងឹតល្មម (Slate 800)
                    'card_bg' => '#1e293b',       // ពណ៌ Card (Slate 800)
                    'dropdown' => '#1e293b',      // ពណ៌ Dropdown ត្រូវតែមានពណ៌ដូច Card (Slate 800)
                    
                    'border_color' => '#334155',  // ពណ៌ស៊ុមសម្រាប់ Dark mode (Slate 700)
                    
                    'text_main' => '#f8fafc',     // អក្សរពណ៌សភ្លឺ (Slate 50)
                    'text_muted' => '#94a3b8',    // អក្សរពណ៌ប្រផេះស្រាល (Slate 400)
                ]
            ]
        ]);
    
    }
}
