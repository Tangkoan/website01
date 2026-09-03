<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema; // 👈 ត្រូវប្រាកដថាមានបន្ទាត់នេះ

use Illuminate\Support\Facades\Gate; // ត្រូវប្រាកដថាបាន import Gate
use App\Models\Category; // ត្រូវ use Model Category នេះ
use Illuminate\Support\Facades\View;
use App\Models\ShopInfo; // Import Model

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // នេះជាចំណុចសំខាន់បំផុត! 
        // វានឹងរត់មុនពេល Render ទំព័រ 404 ឬទំព័រផ្សេងៗ
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            // បើចង់ឲ្យវា Default ខ្មែរតែម្ដង កំណត់វានៅទីនេះ
            App::setLocale(config('app.locale')); 
        }


        // ផ្ដល់សិទ្ធិទាំងអស់ (Bypass) ទៅឲ្យ user ណាដែលមាន role 'super-admin'
        Gate::before(function ($user, $ability) {
            // សូមប្ដូរ 'super-admin' ទៅតាមឈ្មោះ role ជាក់ស្ដែងដែលបងមានក្នុង Database
            return $user->hasRole('Super Admin') ? true : null;
        });


        // ប្រាប់ Laravel ឲ្យបញ្ជូនទិន្នន័យ categories នេះទៅកាន់ component header គ្រប់ពេលវាដើរ
        View::composer('components.layouts.header', function ($view) {
            $view->with('categories', Category::where('is_active', 1)->get());
        });

        // Share shopInfo to all views safely
        try {
            if (Schema::hasTable('shop_infos')) {
                View::share('shopInfo', ShopInfo::first());
            } else {
                View::share('shopInfo', null);
            }
        } catch (\Exception $e) {
            View::share('shopInfo', null);
        }
    }
}
