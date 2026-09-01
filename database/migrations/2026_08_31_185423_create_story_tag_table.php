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
        Schema::create('story_tag', function (Blueprint $table) {
            $table->foreignId('story_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->softDeletes();
            
            // កំណត់ Primary Key រួមគ្នាដើម្បីការពារកុំឲ្យទិន្នន័យជាន់គ្នា
            $table->primary(['story_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_tag');
    }
};
