<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Settings\ThemeManager;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\ChangePassword;
// use App\Livewire\Settings\PermissionTrash;
use Illuminate\Support\Facades\Artisan;


use App\Livewire\Settings\RoleManagement;
use App\Livewire\Settings\RoleTrash;
use App\Livewire\Settings\RoleLogs;

use App\Livewire\Settings\UserManagement;
use App\Livewire\Settings\UserTrash;
use App\Livewire\Settings\UserLogs;

use App\Livewire\Settings\GenericTrash;
use App\Livewire\Settings\GenericLog;




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
    Route::get('/logs/{type}', GenericLog::class)->name('settings.logs');
    
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/settings/theme', ThemeManager::class)->name('settings.theme');

    // Profile
    Route::get('/settings/profile', Profile::class)->name('profile.edit');
    Route::get('/settings/password', ChangePassword::class)->name('password.change');

    // ការពារ Route ទាំងនេះដោយទាមទារសិទ្ធិ view (បងអាចប្ដូរឈ្មោះ permission តាមជាក់ស្ដែង)
    Route::get('/settings/users', \App\Livewire\Settings\UserManagement::class)
        ->can('view-user'); 
        
    Route::get('/settings/permission', \App\Livewire\Settings\PermissionManagement::class)
        ->name('settings.permissions')
        ->can('view-permission'); 

    // Route::get('/settings/permissions/trash', PermissionTrash::class)->name('permissions.trash');

    Route::get('/settings/permissions/logs', PermissionActivityLog::class)->name('permissions.logs');

    // Settings Group
    Route::prefix('settings')->group(function () {
        // Route::get('/permissions', PermissionManagement::class)->name('settings.permissions');
        Route::get('/roles', RoleManagement::class)->name('settings.roles');
        Route::get('/roles/logs', RoleLogs::class)->name('settings.roles.logs');
        
        // ==========================================
        // ផ្នែក Users (បន្ថែមថ្មី)
        // ==========================================
        Route::get('/users', UserManagement::class)
            ->name('settings.users')
            ->can('view-user'); // ការពារដោយ Permission របស់បង

        Route::get('/users/logs', UserLogs::class)
            ->name('settings.users.logs')
            ->can('view-user');

        
    });

    Route::get('/trash/{type}', GenericTrash::class)->name('settings.trash');

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