<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class RoleSetting extends Component
{
    public $roleUiMode = 'hide'; // សម្រាប់គ្រប់គ្រងប៊ូតុងទូទៅ
    public $sidebarUiMode = 'hide'; // សម្រាប់គ្រប់គ្រង Sidebar ថ្មី

    public function mount()
    {
        // ទាញយកទិន្នន័យ Role UI (សម្រាប់ប៊ូតុង)
        $this->roleUiMode = Cache::rememberForever('setting_role_ui_mode', function () {
            $setting = Setting::where('key', 'role_ui_mode')->first();
            return $setting ? $setting->value : 'hide';
        });

        // ទាញយកទិន្នន័យ Sidebar UI (សម្រាប់ Sidebar)
        $this->sidebarUiMode = Cache::rememberForever('setting_sidebar_ui_mode', function () {
            $setting = Setting::where('key', 'sidebar_ui_mode')->first();
            return $setting ? $setting->value : 'hide';
        });
    }

    public function saveSettings()
    {
        // រក្សាទុកឬអាប់ដេត Role UI Mode
        Setting::updateOrCreate(
            ['key' => 'role_ui_mode'],
            [
                'value' => $this->roleUiMode,
                'group' => 'role_permissions'
            ]
        );

        // រក្សាទុកឬអាប់ដេត Sidebar UI Mode
        Setting::updateOrCreate(
            ['key' => 'sidebar_ui_mode'],
            [
                'value' => $this->sidebarUiMode,
                'group' => 'role_permissions'
            ]
        );

        // លុប Cache ចាស់ចោល
        Cache::forget('setting_role_ui_mode');
        Cache::forget('setting_sidebar_ui_mode');

        // បង្ហាញសារជូនដំណឹង
        $this->dispatch('notify', message: __('messages.settings_saved_success') ?? 'Settings saved successfully', type: 'success');
    }

    public function render()
    {
        return view('livewire.settings.role-setting');
    }
}