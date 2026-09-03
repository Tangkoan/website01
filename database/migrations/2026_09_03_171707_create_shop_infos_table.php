<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shop_infos', function (Blueprint $table) {
            $table->id();
            
            // 1. General Info
            $table->string('site_name')->nullable();
            $table->string('site_tagline')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            
            // 2. Contact Info
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            
            // 3. Social Links
            $table->string('facebook_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('twitter_url')->nullable();
            
            // 4. SEO & Analytics
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image')->nullable(); // រូបភាពតំណាងពេល Share Link
            $table->string('google_site_verification')->nullable(); // កូដបញ្ជាក់ភាពជាម្ចាស់ពី Google
            $table->text('google_analytics')->nullable(); // កូដតាមដានអ្នកចូលមើល
            
            // 5. Global Ad Networks Scripts (ដាក់ក្នុង <head> ឬមុន </body>)
            $table->text('adsense_script')->nullable(); // Auto Ads របស់ Google
            $table->text('adsterra_script')->nullable(); // Pop-under ឬ Social Bar របស់ Adsterra
            
            // 6. Ad Banner Placements (កូដ HTML/JS សម្រាប់ដាក់តាមទីតាំងនីមួយៗ)
            $table->text('ad_top_banner')->nullable(); // ផ្ទាំង Banner ក្រោម Header
            $table->text('ad_sidebar_banner')->nullable(); // ផ្ទាំង Banner ក្នុង Sidebar
            $table->text('ad_in_article_banner')->nullable(); // ផ្ទាំង Banner កាត់ចន្លោះកថាខណ្ឌអត្ថបទ
            $table->text('adskeeper_widget')->nullable(); // សម្រាប់កូដ Native Ads ចុងអត្ថបទ "Interesting For You"
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_infos');
    }
};