<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class Sidebar extends Component
{
    // ២. បង្កើតអថេរមួយដើម្បីបញ្ឆោតឱ្យ Livewire ដឹងថា State ផ្លាស់ប្តូរ
    public $currentLocale; 

    // ចាប់យកតម្លៃ locale ដែលបាញ់ចេញពី Header
    #[On('language-updated')]
    public function updateLanguage($locale)
    {
        // ៣. ពេលកំណត់តម្លៃថ្មីឱ្យអថេរនេះ Livewire នឹង Re-render កូដ HTML ថ្មីភ្លាមៗ
        $this->currentLocale = $locale; 
    }

    public function render()
    {
        return view('components.sidebar');
    }
}