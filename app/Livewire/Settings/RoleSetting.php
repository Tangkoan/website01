<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class RoleSetting extends Component
{
    public $roleUiMode = 'hide'; // តម្លៃដើម (Default)

    public function mount()
    {
        // ទាញយកទិន្នន័យពី Cache ដើម្បីកុំឱ្យស្ពឹកប្រព័ន្ធ
        $this->roleUiMode = Cache::rememberForever('setting_role_ui_mode', function () {
            $setting = Setting::where('key', 'role_ui_mode')->first();
            return $setting ? $setting->value : 'hide'; // បើគ្មានទិន្នន័យ យក 'hide'
        });
    }

    public function saveSettings()
    {
        // រក្សាទុក ឬ Update ទៅក្នុង Database
        Setting::updateOrCreate(
            ['key' => 'role_ui_mode'], // ស្វែងរកតាម Key នេះ
            [
                'value' => $this->roleUiMode,
                'group' => 'role_permissions' // ចាត់ចូលក្រុមសិទ្ធិ
            ]
        );

        // លុប Cache ចាស់ចោល ដើម្បីឱ្យប្រព័ន្ធចាប់យកតម្លៃថ្មី
        Cache::forget('setting_role_ui_mode');

        // បង្ហាញសារជូនដំណឹង ដោយប្រើមុខងារបកប្រែ __()
        $this->dispatch('notify', message: __('messages.settings_saved_success'), type: 'success');
    }

    public function render()
    {
        return view('livewire.settings.role-setting');
    }
}