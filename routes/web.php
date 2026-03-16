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

});


Route::fallback(function () {
    abort(404);
});