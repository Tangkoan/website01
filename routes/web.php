<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Settings\ThemeManager;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\ChangePassword;
use App\Livewire\Settings\PermissionTrash;
use Illuminate\Support\Facades\Artisan;

use App\Livewire\Settings\PermissionActivityLog;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->middleware('guest')->name('login');


Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/settings/theme', ThemeManager::class)->name('settings.theme');

    // Profile
    Route::get('/settings/profile', Profile::class)->name('profile.edit');
    Route::get('/settings/password', ChangePassword::class)->name('password.change');

    // ការពារ Route ទាំងនេះដោយទាមទារសិទ្ធិ view (បងអាចប្ដូរឈ្មោះ permission តាមជាក់ស្ដែង)
    Route::get('/settings/users', \App\Livewire\Settings\UserManagement::class)
        ->can('view-user'); 
        
    Route::get('/settings/permission', \App\Livewire\Settings\PermissionManagement::class)
        ->can('view-permission'); 

    Route::get('/settings/permissions/trash', PermissionTrash::class)->name('permissions.trash');

    Route::get('/settings/permissions/logs', PermissionActivityLog::class)->name('permissions.logs');

});

Route::get('/cron/trash/empty/{token}', function ($token) {
    // កំណត់ Token សម្ងាត់មួយ (អ្នកអាចដូរលេខនេះតាមចិត្ត)
    $secretToken = 'SiemReapGear-POS-Secret-Key-9988';
    if ($token !== $secretToken) {
        abort(403, 'Unauthorized Action.');
    }
    // ហៅ Command ដែលយើងបានសរសេរមុននេះ
    Artisan::call('trash:auto-empty');
    return response()->json([
        'status' => 'success',
        'message' => 'ធុងសំរាមត្រូវបានសម្អាតដោយស្វ័យប្រវត្តិ!'
    ]);
});


Route::fallback(function () {
    abort(404);
});