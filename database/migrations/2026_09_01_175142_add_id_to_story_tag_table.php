<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('story_tag', function (Blueprint $table) {
            // ១. ដោះ Foreign Keys ចេញសិន (ប្រើទម្រង់ Array ឲ្យ Laravel រកឈ្មោះខ្សែចំណងដោយខ្លួនឯង)
            $table->dropForeign(['story_id']);
            $table->dropForeign(['tag_id']);

            // ២. ឥឡូវនេះទើបអាចលុប Primary Key ចាស់បានដោយសុវត្ថិភាព
            $table->dropPrimary();

            // ៣. បន្ថែម Field id ជាប្រភេទ Auto-Increment ហើយរុញវាទៅឈរខាងដើមគេ
            $table->id()->first();

            // ៤. ចង Foreign Keys បញ្ចូលមកវិញឲ្យដូចដើម
            $table->foreign('story_id')->references('id')->on('stories')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('story_tag', function (Blueprint $table) {
            // ដោះ Foreign Keys ចេញសិនពេល Rollback
            $table->dropForeign(['story_id']);
            $table->dropForeign(['tag_id']);

            $table->dropColumn('id');
            
            // ចង Primary Key រួមគ្នាវិញ
            $table->primary(['story_id', 'tag_id']);

            // ចង Foreign Keys ចូលវិញ
            $table->foreign('story_id')->references('id')->on('stories')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
        });
    }
};