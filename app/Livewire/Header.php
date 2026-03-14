<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Header extends Component
{
    public function changeLanguage($locale)
{
    if (in_array($locale, ['en', 'km'])) {
        // ១. រក្សាទុកភាសាចូលក្នុង Session
        session()->put('locale', $locale);
        
        // ២. កំណត់ Locale ឱ្យ App ភ្លាមៗ (ដើម្បីឱ្យវាបង្ហាញភាសាថ្មីក្នុង Request នេះតែម្តង)
        app()->setLocale($locale);

        // ៣. Redirect ទៅកាន់ទំព័រដើមវិញដោយប្រើ navigate: true
        // វានឹងទាញយកមាតិកាថ្មីមកប្តូរជំនួសដោយមិន Refresh browser ឡើយ
        return $this->redirect(request()->header('Referer'), navigate: true);
    }
}

    public function logout()
    {
        \Illuminate\Support\Facades\Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/'); // ត្រឡប់ទៅទំព័រ Login
    }

    

    public function render()
    {
        // ប្រាកដថា view នេះស្ថិតក្នុង resources/views/livewire/header.blade.php
        return view('components.header');
    }
}