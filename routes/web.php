<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Settings\ThemeManager;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\ChangePassword;

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

});


Route::fallback(function () {
    abort(404);
});