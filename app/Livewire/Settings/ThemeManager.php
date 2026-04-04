<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Theme;

class ThemeManager extends Component
{
    public $themeId;
    
    // បញ្ចូលពណ៌ស្តង់ដារ (អាចជា Hex ឬ RGBA)
    public $lightColors = [
        'header' => 'rgba(255, 255, 255, 0.9)', 
        'card_bg' => '#ffffff', 
        'primary' => '#ff0000', 
        'sidebar' => 'rgba(255, 255, 255, 0.9)', 
        'dropdown' => 'rgba(255, 255, 255, 0.95)', 
        'text_main' => '#1f2937', 
        'background' => '#f3f4f6', 
        'text_muted' => '#6b7280', 
        'border_color' => 'rgba(229, 231, 235, 1)', 
        'primary_text' => '#ffffff',
        'blur' => '8px'
    ];

    public $darkColors = [
        'header' => 'rgba(30, 41, 59, 0.85)', 
        'card_bg' => '#1e293b', 
        'primary' => '#3b82f6', 
        'sidebar' => 'rgba(30, 41, 59, 0.85)', 
        'dropdown' => 'rgba(30, 41, 59, 0.95)', 
        'text_main' => '#f8fafc', 
        'background' => '#0f172a', 
        'text_muted' => '#94a3b8', 
        'border_color' => 'rgba(51, 65, 85, 1)', 
        'primary_text' => '#ffffff',
        'blur' => '12px'
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
        try{
            $theme = Theme::find($this->themeId);
                    
            if ($theme) {
                $theme->update([
                    'colors' => [
                        'light' => $this->lightColors,
                        'dark' => $this->darkColors,
                    ]
                ]);

                \Illuminate\Support\Facades\Cache::forget('global_theme_colors');

                $this->dispatch('notify', 
                    type: 'success', 
                    message: __('messages.saved_successfully') ?? 'Saved Successfully!'
                );
            }
        }catch (\Exception $e){
            $this->dispatch('notify', 
                type: 'error', 
                message: __('messages.save_error') ?? 'Something went wrong!'
            );
        }
    }

    public function render()
    {
        return view('livewire.settings.theme-manager')->title(__('messages.theme_customization'));
    }
}