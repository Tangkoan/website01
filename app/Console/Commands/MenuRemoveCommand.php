<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Sidebar;
use Illuminate\Support\Facades\Cache;

class MenuRemoveCommand extends Command
{
    protected $signature = 'vc:menu-remove {model : The name of the model (e.g. Test/Demo)}';
    protected $description = 'Remove a Menu item from the Database (Dynamic Sidebar)';

    public function handle()
    {
        $inputModel = $this->argument('model');
        $modelName = Str::studly(class_basename($inputModel)); 
        $modelNameLower = Str::kebab($modelName);
        
        $modelPathRaw = trim(str_replace(class_basename($inputModel), '', $inputModel), '/\\');
        $modelPathParts = array_map(fn($part) => Str::studly($part), explode('/', str_replace('\\', '/', $modelPathRaw)));
        $modelPath = implode('/', array_filter($modelPathParts));

        $routePrefixUrl = empty($modelPath) ? '' : strtolower(str_replace('\\', '/', $modelPath)) . '/';
        $routePath = $routePrefixUrl . Str::plural($modelNameLower);
        
        $this->info("🗑️ Searching Database for Menu: [{$routePath}]...");

        // ស្វែងរក Menu កូននៅក្នុង Database
        $menu = Sidebar::where('url', $routePath)->first();

        if ($menu) {
            $parentId = $menu->parent_id;
            
            // លុប Menu កូនចោល (ប្រើ forceDelete ដើម្បីផ្តាច់ឫស)
            $menu->forceDelete(); 
            $this->info("✅ Menu [{$routePath}] deleted successfully!");

            // ឆែកមើលមេ (Parent): បើមេលែងមានកូនផ្សេងទៀតទេ គឺលុបមេចោលដែរ!
            if ($parentId) {
                $parent = Sidebar::find($parentId);
                if ($parent && $parent->children()->count() === 0) {
                    $parent->forceDelete();
                    $this->info("🧹 Parent Menu [{$parent->title}] was empty and has been deleted too.");
                }
            }

            // Clear Cache ឱ្យវា Update លើ UI ភ្លាមៗ
            Cache::forget('sidebar_dynamic_menus');
            $this->info("🔄 Sidebar cache cleared.");
            
        } else {
            $this->warn("⚠️ Menu [{$routePath}] not found in the Database!");
        }
    }
}