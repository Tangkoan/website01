<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PageController extends Controller
{
    // Function សម្រាប់ទាញឈ្មោះវេបសាយ
    private function getSiteName()
    {
        // ឆែកមើលថាតើមាន Table 'tg_shop_info' ក្នុង Database ដែរឬទេ
        if (Schema::hasTable('tg_shop_info')) {
            $siteName = DB::table('tg_shop_info')->value('name');
            return $siteName ?? 'Life Stories With Us';
        }

        // បើមិនទាន់មាន Table ទេ ដាក់ឈ្មោះ Hard code នេះសិន
        return 'Life Stories With Us'; 
    }

    public function privacyPolicy()
    {
        $siteName = $this->getSiteName();
        return view('frontend.pages.privacy-policy', compact('siteName'));
    }

    public function terms()
    {
        $siteName = $this->getSiteName();
        return view('frontend.pages.terms', compact('siteName'));
    }

    public function about()
    {
        $siteName = $this->getSiteName();
        return view('frontend.pages.about', compact('siteName'));
    }

    public function contact()
    {
        $siteName = $this->getSiteName();
        return view('frontend.pages.contact', compact('siteName'));
    }
}