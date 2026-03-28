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
        // ប្ដូរឈ្មោះទៅ tg_users
        Schema::table('users', function (Blueprint $table) {
            
            $table->boolean('status')->default(true); // true = Active, false = Inactive
            $table->softDeletes(); // សម្រាប់ Trash
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ប្ដូរឈ្មោះទៅ tg_users ដូចគ្នា
        Schema::table('users', function (Blueprint $table) {
            // សរសេរកូដសម្រាប់លុបវិញពេលយើងចង់ Rollback
            $table->dropColumn([ 'status']);
            $table->dropSoftDeletes();
        });
    }
};