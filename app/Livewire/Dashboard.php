<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session; 

class Dashboard extends Component
{


    public function changeLanguage($locale)
    {
        if (in_array($locale, ['en', 'km'])) {
            session()->put('locale', $locale);
            app()->setLocale($locale);
            
            // ស្រែកប្រាប់ Component ផ្សេងៗ (ដូចជា Header) ឱ្យដឹងខ្លួន
            $this->dispatch('locale-updated'); 
        }
    }

    public function logout()
    {
        // ១. ធ្វើការចាកចេញពីគណនី
        Auth::logout();
        
        // ២. សម្អាត Session ដើម្បីសុវត្ថិភាព
        session()->invalidate();
        session()->regenerateToken();

        // ៣. បញ្ជូនត្រឡប់ទៅទំព័រដើមវិញ (មិនបាច់ Refresh ទំព័រ)
        return $this->redirect('/', navigate: true);

        // ដក navigate: true ចេញ ដើម្បីឱ្យវា Refresh ទំព័រពេញលេញ
        // return $this->redirect('/');
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}