<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
    }
}
