<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Theme;

class ThemeManager extends Component
{
    public $themeId;
    
    // បញ្ចូលពណ៌ស្តង់ដារតាម JSON របស់អ្នក
    public $lightColors = [
        'header' => '#ffffff', 'card_bg' => '#ffffff', 'primary' => '#ff0000', 
        'sidebar' => '#ffffff', 'dropdown' => '#ffffff', 'text_main' => '#1f2937', 
        'background' => '#f3f4f6', 'text_muted' => '#6b7280', 'border_color' => '#e5e7eb', 
        'primary_text' => '#ffffff'
    ];

    public $darkColors = [
        'header' => '#1e293b', 'card_bg' => '#1e293b', 'primary' => '#3b82f6', 
        'sidebar' => '#1e293b', 'dropdown' => '#1e293b', 'text_main' => '#f8fafc', 
        'background' => '#0f172a', 'text_muted' => '#94a3b8', 'border_color' => '#334155', 
        'primary_text' => '#ffffff'
    ];

    public function mount()
    {
        $theme = Theme::firstOrCreate(
            ['is_active' => true],
            ['name' => 'Default Theme', 'colors' => [
                'light' => $this->lightColors,
                'dark' => $this->darkColors
            ]]
        );

        $this->themeId = $theme->id;

        if (isset($theme->colors['light'])) {
            $this->lightColors = array_merge($this->lightColors, $theme->colors['light']);
        }
        if (isset($theme->colors['dark'])) {
            $this->darkColors = array_merge($this->darkColors, $theme->colors['dark']);
        }
    }

    public function saveTheme()
    {
        $theme = Theme::find($this->themeId);
        
        if ($theme) {
            $theme->update([
                'colors' => [
                    'light' => $this->lightColors,
                    'dark' => $this->darkColors,
                ]
            ]);

            // បញ្ជូនសញ្ញា (Event) ទៅកាន់ Frontend ថា Save បានជោគជ័យ ដើម្បីបង្ហាញសារ
            $this->dispatch('theme-saved');
        }
    }

    public function render()
    {
        return view('livewire.settings.theme-manager');
    }
}