<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MenuRemoveCommand extends Command
{
    protected $signature = 'vc:menu-remove {model : The name of the model (e.g. Product or Settings/Category)}';
    protected $description = 'Remove a specific Menu item from the Sidebar';

    public function handle()
    {
        $inputModel = $this->argument('model');
        $modelName = class_basename($inputModel);
        
        // ធានាថា Path ត្រូវបានបំលែងទៅជា StudlyCase ដូចពេល Generate
        $modelPathRaw = trim(str_replace($modelName, '', $inputModel), '/\\');
        $modelPathParts = array_map(fn($part) => Str::studly($part), explode('/', str_replace('\\', '/', $modelPathRaw)));
        $modelPath = implode('\\', array_filter($modelPathParts));
        
        $modelName = Str::studly($modelName); 
        $classPath = empty($modelPath) ? $modelName : $modelPath . '\\' . $modelName;

        $sidebarPath = resource_path('views/components/sidebar.blade.php');
        
        if (!File::exists($sidebarPath)) {
            $this->error("❌ Error: 'sidebar.blade.php' not found!");
            return;
        }

        $content = File::get($sidebarPath);
        $escapedClassPath = preg_quote($classPath, '/');
        
        // ប្រើ Regex ស្វែងរក Hook ពី START ដល់ END
        $pattern = "/\s*\{\{-- \[MENU_START_{$escapedClassPath}\] --\}\}.*?\{\{-- \[MENU_END_{$escapedClassPath}\] --\}\}\s*/s";
        
        if (preg_match($pattern, $content)) {
            // លុបកូដ Menu ចេញ ហើយជំនួសដោយ Newline
            $newContent = preg_replace($pattern, "\n        ", $content);
            File::put($sidebarPath, $newContent);
            $this->info("✅ Successfully removed Menu for [{$classPath}] from the Sidebar!");
        } else {
            // ✅ បើរកមិនឃើញ (ប្រហែលជាលុបហើយ ឬអត់ទាន់ Generate)
            $this->warn("ℹ️  Nothing to remove: Menu for [{$classPath}] was not found in the sidebar.");
        }
    }
}