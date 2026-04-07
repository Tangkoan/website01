<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CrudRollbackCommand extends Command
{
    protected $signature = 'vc:rollback {model}';
    protected $description = 'Rollback CRUD files and remove injected codes (Excludes Menu)';

    public function handle()
    {
        $inputModel = $this->argument('model');
        $modelName = class_basename($inputModel);
        
        // ✅ ធានាថា Path ត្រូវបានបំលែងទៅជា StudlyCase ដូចពេល Generate
        $modelPathRaw = trim(str_replace($modelName, '', $inputModel), '/\\');
        $modelPathParts = array_map(fn($part) => Str::studly($part), explode('/', str_replace('\\', '/', $modelPathRaw)));
        $modelPath = implode('\\', array_filter($modelPathParts));
        
        $modelName = Str::studly($modelName); 
        $modelNameLower = Str::kebab($modelName);

        // ឥឡូវនេះ $classPath នឹងមានទម្រង់ 'Product\Category' ជានិច្ច
        $classPath = empty($modelPath) ? $modelName : $modelPath . '\\' . $modelName;
        $viewPathBase = empty($modelPath) ? $modelNameLower : strtolower(str_replace('\\', '/', $modelPath)) . '/' . $modelNameLower;

        $this->info("🔍 Scanning for leftover files of [{$classPath}]...");

        $targets = [
            'service' => app_path("Services/{$modelName}Service.php"),
            'livewire' => empty($modelPath) ? app_path("Livewire/{$modelName}Management.php") : app_path("Livewire/" . str_replace('\\', '/', $classPath) . "Management.php"),
            'view_main' => resource_path("views/livewire/{$viewPathBase}"),
            'view_partials' => resource_path("views/livewire/partials/{$viewPathBase}"),
            'seeder' => database_path("seeders/{$modelName}PermissionSeeder.php"),
            'route' => 'web.php',
            'injections' => 'controllers'
        ];

        $existing = [];
        foreach ($targets as $key => $path) {
            if ($key === 'route' || $key === 'injections') {
                $existing[$key] = $path; // Always try to clean routes and injections
            } elseif (File::exists($path)) {
                $existing[$key] = $path;
            }
        }

        if (empty($existing)) {
            $this->warn("✅ Exit: No generated files found for [{$classPath}]. It's already clean!");
            return;
        }

        if (!$this->confirm("⚠️  Are you sure you want to completely remove the CRUD files for [{$classPath}]?")) return;

        $this->info("🗑️  Rolling back CRUD for [{$classPath}]...");

        foreach ($existing as $key => $path) {
            if ($key === 'route') $this->removeRoute($classPath);
            elseif ($key === 'injections') $this->removeInjections($modelName, $modelNameLower, $modelPath);
            elseif (is_dir($path)) $this->deleteDirectory($path);
            else $this->deleteFile($path);
        }

        $this->info("✅ Rollback completed successfully!");
    }

    protected function deleteFile($path) { if (File::exists($path)) { File::delete($path); $this->line("➖ Deleted: {$path}"); } }
    protected function deleteDirectory($path) { if (File::exists($path)) { File::deleteDirectory($path); $this->line("➖ Deleted Dir: {$path}"); } }

    protected function removeRoute($classPath) {
        $webPhpPath = base_path('routes/web.php');
        $fullClass = "\\App\\Livewire\\" . str_replace('/', '\\', $classPath) . "Management::class";
        
        if (File::exists($webPhpPath)) {
            $content = File::get($webPhpPath);
            // ✅ Regex នេះនឹងលុប Route ទោះបីជាអក្សរតូច ឬធំក៏ដោយ (ប្រើ /i)
            $escapedClass = preg_quote($fullClass, '/');
            $pattern = "/\n?\s*Route::get\(.*{$escapedClass}.*\);/i";
            
            if (preg_match($pattern, $content)) {
                File::put($webPhpPath, preg_replace($pattern, '', $content));
                $this->line("🔗 Removed Route from web.php");
            }
        }
    }

    protected function removeInjections($modelName, $modelNameLower, $modelPath) {
        $logPath = app_path('Livewire/Settings/GenericLog.php');
        $trashPath = app_path('Livewire/Settings/GenericTrash.php');
        $globalTrashPath = app_path('Livewire/Settings/GlobalTrashManager.php');
        
        $routePrefixName = empty($modelPath) ? '' : strtolower(str_replace('\\', '.', $modelPath)) . '.';
        $pluralModelRoute = $routePrefixName . Str::plural($modelNameLower) . '.index'; 

        foreach ([$logPath, $trashPath] as $path) {
            if (File::exists($path)) {
                $content = File::get($path);
                $content = preg_replace("/\n\s*'{$modelNameLower}'\s*=>\s*\\\\App\\\\Models\\\\{$modelName}::class,/", '', $content);
                $content = preg_replace("/\n\s*'{$modelNameLower}'\s*=>\s*'{$pluralModelRoute}',/", '', $content);
                File::put($path, $content);
                $this->line("🧹 Removed injections from " . basename($path));
            }
        }

        if (File::exists($globalTrashPath)) {
            $content = File::get($globalTrashPath);
            File::put($globalTrashPath, preg_replace("/\n\s*'{$modelNameLower}'\s*=>\s*\[.*?\]\s*\],/s", '', $content));
        }
    }
}