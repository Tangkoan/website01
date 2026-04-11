<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Sidebar; 
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
        
        // 🌟 កាត់យកឈ្មោះ Folder ឱ្យបានត្រឹមត្រូវ ទោះឈ្មោះ Folder និង Model ដូចគ្នាក៏ដោយ
        $cleanInput = str_replace('\\', '/', $inputModel);
        $modelPathRaw = dirname($cleanInput);
        $modelPathRaw = $modelPathRaw === '.' ? '' : $modelPathRaw;
        
        $modelPathParts = array_map(fn($part) => Str::studly($part), explode('/', $modelPathRaw));
        $modelPath = implode('/', array_filter($modelPathParts)); 

        $routePrefixUrl = empty($modelPath) ? '' : strtolower(str_replace('\\', '/', $modelPath)) . '/';
        $routePath = $routePrefixUrl . Str::plural($modelNameLower); 
        $permission = "view-{$modelNameLower}";
        
        $this->info("🚀 Generating Database Menu for [{$inputModel}]...");

        // ❖ បើជា Menu ទោល (អត់មាន Group/Folder ពីមុខ)
        if (empty($modelPath)) {
            Sidebar::firstOrCreate(
                ['url' => $routePath], 
                [
                    'parent_id' => null,
                    'name' => $modelNameLower, // 👈 រក្សាទុកត្រឹមពាក្យ 'product'
                    'icon' => '📦',
                    'permission' => $permission,
                    'is_active' => true,
                    'order' => 99,
                ]
            );
            $this->info("✅ Single Menu [{$modelNameLower}] added to Database!");
        } 
        // ❖ បើជា Menu មាន Group (ឧ. Product/Product)
        else {
            $groupNameKey = strtolower(str_replace('/', '_', $modelPath));

            // ១. ស្វែងរក ឬបង្កើត Main Menu (មេ) 
            $parent = Sidebar::firstOrCreate(
                ['name' => $groupNameKey, 'parent_id' => null], // 👈 ស្វែងរកមេឈ្មោះ 'product'
                [
                    'icon' => '📁',
                    'url' => null, 
                    'permission' => null, 
                    'is_active' => true,
                    'order' => 99,
                ]
            );

            // ២. បង្កើត Sub-Menu (កូន) ដោយចងភ្ជាប់ទៅមេ
            Sidebar::firstOrCreate(
                ['url' => $routePath, 'parent_id' => $parent->id],
                [
                    'name' => $modelNameLower, // 👈 រក្សាទុកត្រឹមពាក្យ 'product'
                    'icon' => null, 
                    'permission' => $permission,
                    'is_active' => true,
                    'order' => 99,
                ]
            );

            $this->info("✅ Group Menu [{$groupNameKey}] -> [{$modelNameLower}] added to Database!");
        }

        Cache::forget('sidebar_dynamic_menus');
        $this->info("🧹 Sidebar cache cleared. Menu will appear immediately!");
    }
}