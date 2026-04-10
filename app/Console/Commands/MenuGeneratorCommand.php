<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Sidebar; // ✅ ហៅ Model មកប្រើប្រាស់
use Illuminate\Support\Facades\Cache;

class MenuGeneratorCommand extends Command
{
    protected $signature = 'vc:menu {model : The name of the model (e.g. Product or Settings/Category)}';
    protected $description = 'Inject a new Menu item into the Database (Dynamic Sidebar)';

    public function handle()
    {
        $inputModel = $this->argument('model');
        $modelName = Str::studly(class_basename($inputModel)); 
        $modelNameLower = Str::kebab($modelName);
        
        $modelPathRaw = trim(str_replace(class_basename($inputModel), '', $inputModel), '/\\');
        $modelPathParts = array_map(fn($part) => Str::studly($part), explode('/', str_replace('\\', '/', $modelPathRaw)));
        $modelPath = implode('/', array_filter($modelPathParts)); // ឧ. ប្រែក្លាយ Product\Category ទៅជា Product

        $routePrefixUrl = empty($modelPath) ? '' : strtolower(str_replace('\\', '/', $modelPath)) . '/';
        $routePath = $routePrefixUrl . Str::plural($modelNameLower); // ឧទាហរណ៍៖ product/categories
        $permission = "view-{$modelNameLower}";
        
        $this->info("🚀 Generating Database Menu for [{$inputModel}]...");

        // ❖ បើជា Menu ទោល (អត់មាន Group/Folder ពីមុខ)
        if (empty($modelPath)) {
            $menu = Sidebar::firstOrCreate(
                ['url' => $routePath], // ផ្ទៀងផ្ទាត់កុំឱ្យបង្កើតជាន់គ្នា
                [
                    'parent_id' => null,
                    'name' => $modelNameLower, // ឈ្មោះ Key សម្រាប់បកប្រែ
                    'icon' => '📦',
                    'permission' => $permission,
                    'is_active' => true,
                ]
            );
            $this->info("✅ Single Menu [{$modelName}] added to Database!");
        } 
        // ❖ បើជា Menu មាន Group (ឧ. Product/Category)
        else {
            $groupTitleKey = strtolower(str_replace('/', '_', $modelPath));

            // ១. ស្វែងរក ឬបង្កើត Main Menu (មេ) ជាមុនសិន
            $parent = Sidebar::firstOrCreate(
                ['name' => $groupTitleKey, 'parent_id' => null], 
                [
                    'icon' => '📁',
                    'url' => null, // មេគ្រាន់តែជា Dropdown អត់មាន Link ទេ
                    'permission' => null, // សិទ្ធិយើងឆែកតាមកូនៗរបស់វា
                    'is_active' => true,
                ]
            );

            // ២. បង្កើត Sub-Menu (កូន) ដោយចងភ្ជាប់ parent_id ទៅកាន់មេ
            $child = Sidebar::firstOrCreate(
                ['url' => $routePath, 'parent_id' => $parent->id],
                [
                    'name' => $modelNameLower,
                    'icon' => null, // កូនអត់ដាក់ Icon ទេ ដាក់តែចំណុចៗ
                    'permission' => $permission,
                    'is_active' => true,
                ]
            );

            $this->info("✅ Group Menu [{$modelPath}] -> [{$modelName}] added to Database!");
        }

        // 🧹 លុប Cache របស់ Sidebar ចោល ដើម្បីឱ្យវាទាញទិន្នន័យថ្មីពី DB ភ្លាមៗ
        Cache::forget('sidebar_dynamic_menus');
        $this->info("🧹 Sidebar cache cleared. Menu will appear immediately!");
    }
}