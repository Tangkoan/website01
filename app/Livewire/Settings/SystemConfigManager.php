<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\SystemConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage; // សម្រាប់លុប និងគ្រប់គ្រង File
use Illuminate\Support\Facades\Http;    // សម្រាប់ Download រូបភាពពី URL
use Illuminate\Support\Str;             // សម្រាប់បំប្លែងអក្សរ
use Livewire\WithFileUploads;

class SystemConfigManager extends Component
{
    use WithFileUploads;

    public $activeTab = 'general';
    public $formValues = []; 
    
    public $showBuilder = false;
    public $newConfig = [
        'group' => 'general',
        'name' => '',
        'key' => '',
        'type' => 'string',
        'options' => '' 
    ];

    public $showDeleteModal = false;
    public $configKeyToDelete = null;
    public $configNameToDelete = null;

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $configs = SystemConfig::all();
        foreach ($configs as $config) {
            $safeKey = str_replace('.', '_', $config->key);
            $this->formValues[$safeKey] = $config->value ?? ''; 
        }
    }

    // មុខងារជំនួយ សម្រាប់លុបរូបភាពចាស់ចេញពី Storage
    private function deleteOldImage($path)
    {
        // លុបលុះត្រាតែវាជា Path ក្នុង Local (មិនមែនជា External URL ដែលសេសសល់)
        if ($path && !Str::startsWith($path, ['http://', 'https://'])) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    // ១. ពេលចុចរក្សាទុក (Save)
    public function saveSettings()
    {
        foreach ($this->formValues as $safeKey => $value) {
            $originalKey = str_replace('_', '.', $safeKey);
            $config = SystemConfig::where('key', $originalKey)->first();

            if ($config) {
                $oldValue = $config->value;

                if ($config->type === 'image') {
                    
                    // ករណីទី ១៖ User Upload រូបភាពផ្ទាល់ពីកុំព្យូទ័រ
                    if (is_object($value)) {
                        $value = $value->store('configs', 'public');
                        $this->deleteOldImage($oldValue); // លុបរូបចាស់ចោល
                    } 
                    // ករណីទី ២៖ User Paste Link URL ចូល
                    elseif (is_string($value) && Str::startsWith($value, ['http://', 'https://'])) {
                        try {
                            // ទាញយករូបភាពពី URL នោះ
                            $response = Http::timeout(15)->get($value);
                            
                            if ($response->successful()) {
                                // ចាប់យកកន្ទុយ File (Extension) ដូចជា .png, .jpg
                                $extension = pathinfo(parse_url($value, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
                                $extension = explode('?', $extension)[0]; // កាត់ចោល query params បើមាន

                                // បង្កើតឈ្មោះ File ថ្មីកុំឱ្យជាន់គ្នា
                                $filename = 'configs/' . Str::random(40) . '.' . $extension;

                                // Save រូបដែល Download បានចូល Local Storage យើង
                                Storage::disk('public')->put($filename, $response->body());
                                $value = $filename; // កំណត់តម្លៃថ្មីជា Local Path

                                $this->deleteOldImage($oldValue); // លុបរូបចាស់ចោល
                            }
                        } catch (\Exception $e) {
                            // បើ Download មិនបាន (ឧ. Link ខូច) យើងរក្សាតម្លៃដើមវាទុកសិន
                            $value = $oldValue;
                            $this->dispatch('notify', message: "មិនអាចទាញយករូបភាពពី URL របស់ {$config->name} បានទេ", type: 'error');
                        }
                    }
                    // ករណីទី ៣៖ User លុប Link ចោល (Empty) ចង់បញ្ជាក់ថាឈប់ដាក់រូបហើយ
                    elseif (empty($value) && !empty($oldValue)) {
                        $this->deleteOldImage($oldValue);
                        $value = null;
                    }
                }

                // Update Database
                $config->update(['value' => $value]);
            }
        }

        Cache::forget('global_system_configs');
        
        

        $this->dispatch('notify', message: __('messages.settings_saved_success') ?? 'រក្សាទុកការកែប្រែដោយជោគជ័យ!', type: 'success');
    }

    // ២. មុខងារសម្រាប់ត្រៀមលុប (បើក Modal)
    public function confirmDelete($key, $name)
    {
        $this->configKeyToDelete = $key;
        $this->configNameToDelete = $name;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->configKeyToDelete = null;
        $this->configNameToDelete = null;
    }

    // ៣. ពេលចុចលុប (Delete) ត្រូវលុបទាំងទិន្នន័យ ទាំងរូបភាពចាស់ចោល
    public function executeDelete()
    {
        if ($this->configKeyToDelete) {
            $config = SystemConfig::where('key', $this->configKeyToDelete)->first();
            
            if ($config) {
                // បើ Type ជារូបភាព ត្រូវលុបរូបភាពចេញពី Folder ជាមុនសិន
                if ($config->type === 'image') {
                    $this->deleteOldImage($config->value);
                }

                $config->delete(); // លុបពី Database
                
                $safeKey = str_replace('.', '_', $this->configKeyToDelete);
                unset($this->formValues[$safeKey]);

                
                $this->loadSettings();
                Cache::forget('global_system_configs');
                
                $this->dispatch('notify', message: __('messages.delete_success') ?? 'បានលុបដោយជោគជ័យ!', type: 'success');
            }
        }

        $this->cancelDelete();
    }

    public function createConfig()
    {
        $this->validate([
            'newConfig.name' => 'required|string',
            'newConfig.key' => 'required|string|unique:system_configs,key',
            'newConfig.type' => 'required|string',
        ]);

        $optionsArray = null;
        if ($this->newConfig['type'] === 'select' && !empty($this->newConfig['options'])) {
            $pairs = explode(',', $this->newConfig['options']);
            $optionsArray = [];
            foreach ($pairs as $pair) {
                if (strpos($pair, ':') !== false) {
                    list($k, $v) = explode(':', $pair);
                    $optionsArray[trim($k)] = trim($v);
                }
            }
        }

        $newKey = strtolower(str_replace(' ', '_', $this->newConfig['key']));

        SystemConfig::create([
            'group'   => $this->newConfig['group'],
            'name'    => $this->newConfig['name'],
            'key'     => $newKey, 
            'type'    => $this->newConfig['type'],
            'options' => $optionsArray,
        ]);

        

        $this->showBuilder = false;
        $this->newConfig = ['group' => $this->activeTab, 'name' => '', 'key' => '', 'type' => 'string', 'options' => ''];
        $this->loadSettings();
        
        $this->dispatch('notify', message: __('messages.create_success') ?? 'បង្កើតការកំណត់ថ្មីជោគជ័យ!', type: 'success');
    }

    public function render()
    {
        $groups = SystemConfig::select('group')->distinct()->pluck('group');
        
        if ($groups->isEmpty()) {
            $groups = collect(['general']);
        }

        if (!$groups->contains($this->activeTab)) {
            $this->activeTab = $groups->first();
        }
        
        $configs = SystemConfig::where('group', $this->activeTab)->get();

        return view('livewire.settings.system-config-manager', compact('groups', 'configs'));
    }
}