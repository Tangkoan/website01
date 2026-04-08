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
        
        $modelPathRaw = trim(str_replace($modelName, '', $inputModel), '/\\');
        $modelPathParts = array_map(fn($part) => Str::studly($part), explode('/', str_replace('\\', '/', $modelPathRaw)));
        $modelPath = implode('\\', array_filter($modelPathParts));
        
        $modelName = Str::studly($modelName); 
        $modelNameLower = Str::kebab($modelName);

        $classPath = empty($modelPath) ? $modelName : $modelPath . '\\' . $modelName;
        $viewPathBase = empty($modelPath) ? $modelNameLower : strtolower(str_replace('\\', '/', $modelPath)) . '/' . $modelNameLower;

        $this->info("🔍 Scanning for leftover files of [{$classPath}]...");

        $targets = [
            'model' => app_path("Models/{$modelName}.php"), // ✅ ត្រូវលុប Model វិញ
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
                $existing[$key] = $path; 
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
        if (!File::exists($webPhpPath)) return;
        
        $content = File::get($webPhpPath);
        
        $modelName = class_basename($classPath);
        $className = "{$modelName}Management";
        $fullClassName = preg_quote("App\\Livewire\\" . str_replace('/', '\\', $classPath) . "Management", '/');
        
        // 1. លុប Use Statement នៅខាងលើ
        $usePattern = "/\n?use\s+{$fullClassName};\n?/";
        $content = preg_replace($usePattern, '', $content);
        
        // 2. លុប Route នៅខាងក្នុង
        $routePattern = "/\n?\s*Route::get\(.*{$className}::class.*\);/i";
        $content = preg_replace($routePattern, '', $content);
        
        // 3. ឆ្លាតវៃ៖ លុប Group ណាដែលទទេ (គ្មាន Route ខាងក្នុង) ចោលវិញ
        $emptyGroupPattern = "/\n?Route::prefix\([^)]+\)->name\([^)]+\)->group\(\s*function\s*\(\)\s*\{\s*\}\);\n?/";
        $content = preg_replace($emptyGroupPattern, "\n", $content);

        File::put($webPhpPath, $content);
        $this->line("🔗 Cleaned up Route and 'use' statements from web.php");
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