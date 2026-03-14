<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // បើមានការកំណត់ភាសាក្នុង Session, យកវាមកប្រើ។ បើគ្មានទេ យក 'km' ជាភាសាដើម
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            App::setLocale('km'); 
        }

        return $next($request);
    }
}