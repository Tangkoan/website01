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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            
            // បន្ថែម parent_id ដើម្បីសម្គាល់ Sub-brand (ភ្ជាប់ទៅកាន់ id របស់ table នេះផ្ទាល់)
            $table->foreignId('parent_id')->nullable()->constrained('brands')->cascadeOnDelete();
            
            $table->string('name');
            $table->boolean('status')->default(true);
            $table->string('image')->nullable();
            $table->json('images')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
