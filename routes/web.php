<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Settings\ThemeManager;

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

});


Route::fallback(function () {
    abort(404);
});