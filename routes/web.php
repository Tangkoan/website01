<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ១. ទំព័រដើម (ដែលមាន Form Register / Login)
// យើងប្រើ middleware('guest') ដើម្បីការពារកុំឱ្យអ្នកដែល Login រួចចូលមកទំព័រនេះបានទៀត
// យើងដាក់ឈ្មោះវាថា ->name('login') ព្រោះ Laravel ត្រូវការស្គាល់ឈ្មោះនេះ ដើម្បីរុញអ្នកមិនទាន់ Login មកទីនេះវិញ។
Route::get('/', function () {
    return view('welcome');
})->middleware('guest')->name('login');


// ២. ទំព័រ Dashboard
// យើងប្រើ middleware('auth') ដើម្បីការពារកុំឱ្យអ្នកមិនទាន់ Login លួចចូលមើលបាន
Route::get('/dashboard', Dashboard::class)
    ->middleware('auth')
    ->name('dashboard');

Route::fallback(function () {
    abort(404);
});
