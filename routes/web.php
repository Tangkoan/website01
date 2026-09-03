<?php

use App\Livewire\StoryManagement;use App\Livewire\SettingManagement;use App\Livewire\StoryTagManagement;
use App\Livewire\TagManagement;
use App\Livewire\CategoryManagement;
use App\Livewire\Settings\SidebarManagement;
use App\Livewire\Product\BrandManagement;
// use App\Livewire\Product\CategoryManagement;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\PageController;

// Livewire Components
use App\Livewire\Dashboard;
use App\Livewire\Settings\ThemeManager;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\ChangePassword;

use App\Livewire\Settings\RoleManagement;
use App\Livewire\Settings\RoleLogs;

use App\Livewire\Settings\UserManagement;
use App\Livewire\Settings\UserLogs;

use App\Livewire\Settings\PermissionManagement;
use App\Livewire\Settings\PermissionActivityLog;

use App\Livewire\Settings\GenericTrash;
use App\Livewire\Settings\GenericLog;

use App\Livewire\Settings\RoleSetting;
use App\Livewire\Settings\SystemConfigManager;
use App\Livewire\Settings\ActivityLogManager;
use App\Livewire\Settings\GlobalTrashManager;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ទំព័រ Login (សម្រាប់អ្នកមិនទាន់ Login)
Route::get('/login', function () {
    return view('welcome');
})->middleware('guest')->name('login');
;

Route::post('/summernote-upload', [App\Http\Controllers\UploadController::class, 'summernoteUpload'])->name('summernote.upload');

// ក្រុម Route សម្រាប់អ្នកដែលបាន Login រួច (Auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/story/stories', StoryManagement::class)->name('stories.index')->can('view-story');
    Route::get('/settings', SettingManagement::class)->name('settings.index')->can('view-setting');
    Route::get('/story/story-tags', StoryTagManagement::class)->name('story-tags.index')->can('view-story-tag');
    Route::get('/story/tags', TagManagement::class)->name('tags.index')->can('view-tag');
    Route::get('/story/categories', CategoryManagement::class)->name('categories.index')->can('view-category');

    
    // បង្កើត Route ទៅកាន់ទំព័រ Config
    Route::get('/settings/configs', SystemConfigManager::class)->name('settings.configs')->can('manage_system_configs');
    Route::get('/settings/action', ActivityLogManager::class)->name('settings.action')->can('view-activity-logs');
    Route::get('/settings/recycle', GlobalTrashManager::class)->name('settings.recycle-bin'); // សិទ្ធិជាក់លាក់ត្រូវបានឆែកក្នុងកូដ PHP តាម Tab រួចហើយ
    
    // ==========================================
    // Profile & Password (គ្រប់គ្នាដែល Login អាចចូលបាន)
    // ==========================================
    Route::get('/settings/profile', Profile::class)->name('profile.edit');
    Route::get('/settings/password', ChangePassword::class)->name('password.change');

    // ==========================================
    // Dashboard
    // ==========================================
    Route::get('/dashboard', Dashboard::class)
        ->name('dashboard')
        ->can('view_dashboard');

    


    // ==========================================
    // Settings Group
    // ==========================================
    Route::prefix('settings')->group(function () {
        Route::get('/sidebars', SidebarManagement::class)->name('settings.sidebars.index')->can('view-sidebar');

       Route::get('/role-ui', RoleSetting::class)->name('settings.role-ui')->can('manage_role_ui');
        
        // --- Theme ---
        Route::get('/theme', ThemeManager::class)
            ->name('settings.theme')
            ->can('view_theme');

        // --- Permissions ---
        Route::get('/permission', PermissionManagement::class)
            ->name('settings.permissions')
            ->can('view_permissions');
            
        

        // --- Roles ---
        Route::get('/roles', RoleManagement::class)
            ->name('settings.roles')
            ->can('view_roles');
            
            
        // --- Users ---
        Route::get('/users', UserManagement::class)
            ->name('settings.users')
            ->can('view_users');

        
    });

    // ==========================================
    // Generic Logs & Trash (ទំព័រទូទៅ)
    // ==========================================
    Route::get('/logs/{type}', GenericLog::class)
        ->name('settings.logs')
        ->can('view_logs');
        
    Route::get('/trash/{type}', GenericTrash::class)
        ->name('settings.trash');
        // ->can('view_trash');
});


// ==========================================
// Cron Jobs / APIs
// ==========================================
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


// ==========================================
// Fallback Route (ទំព័រ 404)
// ==========================================
Route::fallback(function () {
    abort(404);
});




// ទំព័រដើម (Home Page)
// ប្ដូរពី Route ដែល return view ផ្ទាល់ មកហៅតាម Controller វិញ
Route::get('/', [FrontendController::class, 'index'])->name('home');
// ដាក់កូដថ្មីនេះជំនួសវិញ (ត្រូវប្រាកដថាអ្នកបាន use App\Http\Controllers\FrontendController; នៅខាងលើគេ)
Route::get('/story/{slug}', [App\Http\Controllers\FrontendController::class, 'show'])->name('story.detail');


// ឧទាហរណ៍ Route សម្រាប់ Menu ខាងលើ
Route::get('/read-with-us', function () {
    return view('frontend.category', ['category' => 'Read With Us']);
})->name('read-with-us');

Route::get('/real-story', function () {
    return view('frontend.category', ['category' => 'Real Story']);
})->name('real-story');

Route::get('/news-healthy', function () {
    return view('frontend.category', ['category' => 'News & Healthy']);
})->name('news-healthy');

// Routes ថ្មីសម្រាប់ Static Pages
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('page.privacy');
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('page.terms');
Route::get('/about-us', [PageController::class, 'about'])->name('page.about');
Route::get('/contact-us', [PageController::class, 'contact'])->name('page.contact');

Route::get('/category/{slug}', [FrontendController::class, 'category'])->name('category.show');




