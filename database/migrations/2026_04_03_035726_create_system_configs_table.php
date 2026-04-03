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
        Schema::create('system_configs', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general'); // សម្រាប់បែងចែក Tab (ឧ. general, email, contact)
            $table->string('name');                      // ឈ្មោះបង្ហាញលើ UI (ឧ. Site Name)
            $table->string('key')->unique();             // Key សម្រាប់ទាញយក (ឧ. site.name)
            $table->string('type')->default('string');   // ប្រភេទ input (string, text, boolean, select)
            $table->text('value')->nullable();           // តម្លៃរបស់វា
            $table->json('options')->nullable();         // ជម្រើសសម្រាប់ Dropdown (បើមាន)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_configs');
    }
};
