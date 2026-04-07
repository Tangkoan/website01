<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CrudGeneratorCommand extends Command
{
    protected $signature = 'vc:crud {model : The name of the model (e.g. Product or Settings/Category)}';
    protected $description = 'Generate complete CRUD files and inject into controllers';

    public function handle()
    {
        $inputModel = $this->argument('model');
        $modelName = class_basename($inputModel);
        
        // ✅ ធានាថា Path ត្រូវបានបំលែងទៅជា StudlyCase (ឧទាហរណ៍ product -> Product)
        $modelPathRaw = trim(str_replace($modelName, '', $inputModel), '/\\');
        $modelPathParts = array_map(fn($part) => Str::studly($part), explode('/', str_replace('\\', '/', $modelPathRaw)));
        $modelPath = implode('\\', array_filter($modelPathParts));
        
        $modelName = Str::studly($modelName); 
        $modelNameLower = Str::kebab($modelName);
        $tableName = Str::plural(Str::snake($modelName));

        // ឥឡូវនេះ $classPath នឹងមានទម្រង់ 'Product\Category' ជានិច្ច ទោះវាយ 'product/category' ក៏ដោយ
        $classPath = empty($modelPath) ? $modelName : $modelPath . '\\' . $modelName;
        $viewPathBase = empty($modelPath) ? $modelNameLower : strtolower(str_replace('\\', '/', $modelPath)) . '/' . $modelNameLower;
        $viewNamespace = str_replace('/', '.', $viewPathBase);

        $this->info("🚀 Starting CRUD Generation for [{$classPath}]...");

        if (!Schema::hasTable($tableName)) {
            $this->error("❌ Error: Table '{$tableName}' does not exist in the database.");
            return;
        }

        $this->generateFile('service.stub', app_path("Services/{$modelName}Service.php"), ['{{ModelName}}' => $modelName]);

        $livewireClassDest = empty($modelPath) ? app_path("Livewire/{$modelName}Management.php") : app_path("Livewire/" . str_replace('\\', '/', $classPath) . "Management.php");
        $namespace = empty($modelPath) ? 'App\Livewire' : 'App\Livewire\\' . $modelPath;

        $this->generateFile('livewire.stub', $livewireClassDest, [
            '{{Namespace}}' => $namespace, '{{ModelName}}' => $modelName, '{{modelNameLower}}' => $modelNameLower, '{{ViewNamespace}}' => $viewNamespace, '{{ModelNamePlural}}' => Str::plural($modelName),
        ]);

        $viewDir = resource_path("views/livewire/{$viewPathBase}");
        $partialsDir = resource_path("views/livewire/partials/{$viewPathBase}");
        File::ensureDirectoryExists($viewDir, 0755, true);
        File::ensureDirectoryExists($partialsDir, 0755, true);

        $replacements = [
            '{{ModelName}}' => $modelName, '{{modelNameLower}}' => $modelNameLower, '{{ModelNamePlural}}' => Str::plural($modelName),
            '{{ViewNamespace}}' => $viewNamespace,
            '{{PartialsNamespace}}' => "partials.{$viewNamespace}"
        ];

        $this->generateFile('view-main.stub', "{$viewDir}/{$modelNameLower}-management.blade.php", $replacements);
        $this->generateFile('view-header.stub', "{$partialsDir}/header.blade.php", $replacements);
        $this->generateFile('view-filters.stub', "{$partialsDir}/filters.blade.php", $replacements);
        $this->generateFile('view-table.stub', "{$partialsDir}/table.blade.php", $replacements);
        $this->generateFile('view-cards-mobile.stub', "{$partialsDir}/cards-mobile.blade.php", $replacements);
        $this->generateFile('view-modal-form.stub', "{$partialsDir}/modal-form.blade.php", $replacements);
        $this->generateFile('view-modal-bulk-edit.stub', "{$partialsDir}/modal-bulk-edit.blade.php", $replacements);

        $this->appendRoute($modelName, $modelPath, $classPath);
        $this->generateFile('seeder-permission.stub', database_path("seeders/{$modelName}PermissionSeeder.php"), $replacements);
        $this->injectIntoGenericControllers($modelName, $modelNameLower, $modelPath);

        $this->info("✅ CRUD Files for {$classPath} generated successfully!");
    }

    protected function generateFile($stubName, $destinationPath, $replacements) {
        if (File::exists($destinationPath)) {
            if (!$this->confirm("⚠️  File [{$destinationPath}] already exists. Overwrite?")) return;
        }
        $stubPath = base_path("stubs/{$stubName}");
        $content = str_replace(array_keys($replacements), array_values($replacements), File::get($stubPath));
        File::ensureDirectoryExists(dirname($destinationPath), 0755, true);
        File::put($destinationPath, $content);
        $this->line("👉 Created/Updated: {$destinationPath}");
    }

    protected function appendRoute($modelName, $modelPath, $classPath) {
        $webPhpPath = base_path('routes/web.php');
        $routePrefixUrl = empty($modelPath) ? '' : strtolower(str_replace('\\', '/', $modelPath)) . '/';
        $routePrefixName = empty($modelPath) ? '' : strtolower(str_replace('\\', '.', $modelPath)) . '.';
        
        $routeUrl = $routePrefixUrl . Str::plural(Str::kebab($modelName)); 
        $routeName = $routePrefixName . Str::plural(Str::kebab($modelName)); 

        $fullClass = "\\App\\Livewire\\" . str_replace('/', '\\', $classPath) . "Management::class";
        $routeCode = "\nRoute::get('/{$routeUrl}', {$fullClass})->name('{$routeName}.index');";
        
        if (!str_contains(File::get($webPhpPath), $fullClass)) {
            File::append($webPhpPath, $routeCode);
            $this->line("🔗 Added Route: {$routeName}.index into web.php");
        }
    }

    protected function injectIntoGenericControllers($modelName, $modelNameLower, $modelPath) {
        $logPath = app_path('Livewire/Settings/GenericLog.php');
        $trashPath = app_path('Livewire/Settings/GenericTrash.php');
        $globalTrashPath = app_path('Livewire/Settings/GlobalTrashManager.php');

        $mapEntry = "\n            '{$modelNameLower}' => \App\Models\\{$modelName}::class,";
        
        $routePrefixName = empty($modelPath) ? '' : strtolower(str_replace('\\', '.', $modelPath)) . '.';
        $pluralModelRoute = $routePrefixName . Str::plural($modelNameLower) . '.index'; 
        
        $backRouteEntry = "\n            '{$modelNameLower}' => '{$pluralModelRoute}',";

        foreach ([$logPath, $trashPath] as $path) {
            if (File::exists($path)) {
                $content = File::get($path);
                $isUpdated = false;
                if (!str_contains($content, "'{$modelNameLower}' => \App\Models")) {
                    $content = preg_replace('/(protected function getModelMap\(\)\s*\{\s*return\s*\[)(.*?)(\s*\];\s*\})/s', "$1$2$mapEntry$3", $content);
                    $isUpdated = true;
                }
                if (!str_contains($content, "'{$modelNameLower}' => '{$pluralModelRoute}'")) {
                    $content = preg_replace('/(public function getBackRoute\(\)\s*\{\s*\$routes\s*=\s*\[)(.*?)(\s*\];\s*return\s*\$routes\[\$this->type\])/s', "$1$2$backRouteEntry$3", $content);
                    $isUpdated = true;
                }
                if ($isUpdated) File::put($path, $content);
            }
        }

        if (File::exists($globalTrashPath)) {
            $content = File::get($globalTrashPath);
            if (!str_contains($content, "'{$modelNameLower}' => [")) {
                $trashConfig = "\n            '{$modelNameLower}' => [\n                'model' => \App\Models\\{$modelName}::class,\n                'icon'  => '📦',\n                'label' => __('messages.{$modelNameLower}_management') ?? '" . Str::plural($modelName) . "',\n                'permissions' => [\n                    'view'    => 'view-{$modelNameLower}-trash',\n                    'restore' => 'restore-{$modelNameLower}',\n                    'delete'  => 'force-delete-{$modelNameLower}',\n                ]\n            ],";
                File::put($globalTrashPath, preg_replace('/(public function getTrashModulesProperty\(\)\s*\{\s*return\s*\[)(.*?)(\s*\];\s*\})/s', "$1$2$trashConfig$3", $content));
            }
        }
    }
}