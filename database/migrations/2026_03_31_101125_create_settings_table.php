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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            // key សម្រាប់ចំណាំ (ឧទាហរណ៍: 'role_ui_mode', 'site_name')
            $table->string('key')->unique(); 
            // value អាចជាអក្សរវែងៗ ឬ JSON (ទើបប្រើ text)
            $table->text('value')->nullable(); 
            // group សម្រាប់បែងចែកក្រុម (ឧទាហរណ៍: 'general', 'security', 'role') ងាយស្រួលទាញយកជាក្រុមថ្ងៃក្រោយ
            $table->string('group')->default('general'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
