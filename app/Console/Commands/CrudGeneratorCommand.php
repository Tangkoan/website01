<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrudGeneratorCommand extends Command
{
    protected $signature = 'vc:crud {model : The name of the model (e.g. Product or Settings/Category)}';
    protected $description = 'Generate complete CRUD with Smart Schema Analyzer, AJAX Summernote, and UI Fixes';

    public function handle()
    {
        $inputModel = $this->argument('model');
        $modelName = class_basename($inputModel);
        
        $modelPathRaw = trim(str_replace($modelName, '', $inputModel), '/\\');
        $modelPathParts = array_map(fn($part) => Str::studly($part), explode('/', str_replace('\\', '/', $modelPathRaw)));
        $modelPath = implode('\\', array_filter($modelPathParts));
        
        $modelName = Str::studly($modelName); 
        $modelNameLower = Str::kebab($modelName);
        $baseTableName = Str::plural(Str::snake($modelName));

        $classPath = empty($modelPath) ? $modelName : $modelPath . '\\' . $modelName;
        $viewPathBase = empty($modelPath) ? $modelNameLower : strtolower(str_replace('\\', '/', $modelPath)) . '/' . $modelNameLower;
        $viewNamespace = str_replace('/', '.', $viewPathBase);

        $this->info("🚀 Starting Dynamic CRUD Generation for [{$classPath}]...");

        $tableName = $this->discoverTableName($baseTableName);

        if (!$tableName) {
            $this->error("❌ Error: Could not find any table matching '{$baseTableName}' or '*_{$baseTableName}' in the database.");
            return;
        }

        $dbPrefix = \Illuminate\Support\Facades\DB::connection()->getTablePrefix();
        $modelTableName = $tableName;
        
        if (!empty($dbPrefix) && Str::startsWith($modelTableName, $dbPrefix)) {
            $modelTableName = Str::replaceFirst($dbPrefix, '', $modelTableName);
        }

        $dynamicData = $this->generateDynamicFields($tableName);

        $this->generateFile('model.stub', app_path("Models/{$modelName}.php"), [
            '{{ModelName}}' => $modelName, 
            '{{TableName}}' => $modelTableName, 
            '{{ModelCasts}}' => $dynamicData['modelCasts'] ?? ''
        ]);
        
        $this->generateFile('service.stub', app_path("Services/{$modelName}Service.php"), ['{{ModelName}}' => $modelName]);

        $livewireClassDest = empty($modelPath) ? app_path("Livewire/{$modelName}Management.php") : app_path("Livewire/" . str_replace('\\', '/', $classPath) . "Management.php");
        $namespace = empty($modelPath) ? 'App\Livewire' : 'App\Livewire\\' . $modelPath;

        $this->generateFile('livewire.stub', $livewireClassDest, [
            '{{Namespace}}' => $namespace, '{{ModelName}}' => $modelName, '{{modelNameLower}}' => $modelNameLower, '{{ViewNamespace}}' => $viewNamespace, '{{ModelNamePlural}}' => Str::plural($modelName),
            '{{FileUploadTrait}}' => $dynamicData['hasFile'] ? "use Livewire\WithFileUploads;\n" : "",
            '{{FileUploadTraitUse}}' => $dynamicData['hasFile'] ? "use WithFileUploads;\n" : "",
            '{{LivewireProperties}}' => $dynamicData['livewireProps'],
            '{{LivewireEditBindings}}' => $dynamicData['livewireEdit'],
            '{{LivewireRules}}' => $dynamicData['livewireRules'],
            '{{LivewireSaveData}}' => $dynamicData['livewireSave'],
            '{{LivewireResetData}}' => $dynamicData['livewireReset'],
            '{{LivewireBulkProperties}}' => $dynamicData['livewireBulkProps'],
            '{{LivewireBulkEditBindings}}' => $dynamicData['livewireBulkEdit'],
            '{{LivewireBulkRules}}' => $dynamicData['livewireBulkRules'],
            '{{LivewireBulkSaveData}}' => $dynamicData['livewireBulkSave'],
            '{{LivewireBulkResetData}}' => $dynamicData['livewireBulkReset'],
            '{{DynamicAvailableColumns}}' => $dynamicData['availableColsStr'],
            '{{DynamicSelectedColumns}}' => $dynamicData['selectedColsStr']
        ]);

        $viewDir = resource_path("views/livewire/{$viewPathBase}");
        $partialsDir = resource_path("views/livewire/partials/{$viewPathBase}");
        File::ensureDirectoryExists($viewDir, 0755, true);
        File::ensureDirectoryExists($partialsDir, 0755, true);

        $replacements = [
            '{{ModelName}}' => $modelName, '{{modelNameLower}}' => $modelNameLower, '{{ModelNamePlural}}' => Str::plural($modelName),
            '{{ViewNamespace}}' => $viewNamespace, '{{PartialsNamespace}}' => "partials.{$viewNamespace}",
            '{{DynamicFormFields}}' => $dynamicData['formHtml'],
            '{{DynamicBulkFormFields}}' => $dynamicData['bulkHtml'],
            '{{DynamicTableHeaders}}' => $dynamicData['tableHeaders'],
            '{{DynamicTableCells}}' => $dynamicData['tableCells'],
            '{{DynamicMobileCells}}' => $dynamicData['mobileCells']
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

        $this->info("✅ CRUD Files generated successfully with all Advanced UI & Logic fixes!");
    }

    protected function discoverTableName($baseName) {
        $tablesInDb = DB::select('SHOW TABLES');
        $tables = []; foreach ($tablesInDb as $t) { $tables[] = current((array) $t); }
        foreach ($tables as $table) { if ($table !== $baseName && Str::endsWith($table, '_' . $baseName)) { return $table; } }
        if (in_array($baseName, $tables)) { return $baseName; } return null;
    }

    protected function generateFile($stubName, $destinationPath, $replacements) {
        if (File::exists($destinationPath)) { if (!$this->confirm("⚠️ File [{$destinationPath}] already exists. Overwrite?")) return; }
        $stubPath = base_path("stubs/{$stubName}");
        $content = str_replace(array_keys($replacements), array_values($replacements), File::get($stubPath));
        File::ensureDirectoryExists(dirname($destinationPath), 0755, true);
        File::put($destinationPath, $content);
    }

    protected function appendRoute($modelName, $modelPath, $classPath) {
        $webPhpPath = base_path('routes/web.php');
        $content = File::get($webPhpPath);
        $className = "{$modelName}Management";
        $fullClassName = "App\\Livewire\\" . ($modelPath ? str_replace('/', '\\', $modelPath) . '\\' : '') . $className;
        $useStatement = "use {$fullClassName};";
        if (!str_contains($content, $useStatement)) $content = preg_replace('/<\?php\s+/', "<?php\n\n{$useStatement}\n", $content, 1);
        $modelNameLower = Str::kebab($modelName); $routeSlug = Str::plural($modelNameLower); $permission = "view-{$modelNameLower}";
        if (empty($modelPath)) {
            $routeCode = "\nRoute::get('/{$routeSlug}', {$className}::class)->name('{$routeSlug}.index')->can('{$permission}');";
            if (!str_contains($content, "{$className}::class")) $content .= $routeCode;
        } else {
            $prefix = strtolower(str_replace('\\', '/', $modelPath)); $prefixName = str_replace('/', '.', $prefix); 
            $innerRoute = "    Route::get('/{$routeSlug}', {$className}::class)->name('{$routeSlug}.index')->can('{$permission}');";
            $groupPattern = "/(Route::prefix\(['\"]{$prefix}['\"]\).*?->group\(\s*function\s*\(\)\s*\{)/is";
            if (preg_match($groupPattern, $content)) { if (!str_contains($content, "{$className}::class")) $content = preg_replace($groupPattern, "$1\n{$innerRoute}", $content, 1);
            } else { $groupCode = "\nRoute::prefix('{$prefix}')->name('{$prefixName}.')->group(function () {\n{$innerRoute}\n});\n";
                if (!str_contains($content, "{$className}::class")) $content .= $groupCode; }
        }
        File::put($webPhpPath, $content);
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
            if (\Illuminate\Support\Facades\File::exists($path)) {
                $content = \Illuminate\Support\Facades\File::get($path);
                
                if (!str_contains($content, "'{$modelNameLower}' => \App\Models")) {
                    $content = preg_replace('/(getModelMap\(\)\s*\{.*?return\s*\[)(.*?)(\s*\];\s*\})/s', "$1$2$mapEntry$3", $content);
                }
                
                // បញ្ចូលចូលក្នុង $backRouteMap នៃ Function render() 
                if (!str_contains($content, "'{$modelNameLower}' => '{$pluralModelRoute}'")) {
                    $content = preg_replace('/(\$backRouteMap\s*=\s*\[)(.*?)(\s*\];)/s', "$1$2$backRouteEntry$3", $content);
                }
                
                \Illuminate\Support\Facades\File::put($path, $content);
            }
        }

        if (\Illuminate\Support\Facades\File::exists($globalTrashPath)) {
            $content = \Illuminate\Support\Facades\File::get($globalTrashPath);
            if (!str_contains($content, "'{$modelNameLower}' => [")) {
                $trashConfig = "\n            '{$modelNameLower}' => [\n                'model' => \App\Models\\{$modelName}::class,\n                'icon'  => '📦',\n                'label' => __('messages.{$modelNameLower}_management') ?? '" . Str::plural($modelName) . "',\n                'permissions' => [\n                    'view'    => 'view-{$modelNameLower}-trash',\n                    'restore' => 'restore-{$modelNameLower}',\n                    'delete'  => 'force-delete-{$modelNameLower}',\n                ]\n            ],";
                $content = preg_replace('/(public function getTrashModulesProperty\(\)\s*\{\s*return\s*\[)(.*?)(\s*\];\s*\})/s', "$1$2$trashConfig$3", $content);
                \Illuminate\Support\Facades\File::put($globalTrashPath, $content);
            }
        }
    }

    private function generateDynamicFields($tableName) {
        $columnsData = [];
        $cols = DB::select('SHOW COLUMNS FROM ' . $tableName);
        foreach ($cols as $c) { $columnsData[] = ['name' => $c->Field, 'type_name' => preg_replace('/\(.*\)/', '', $c->Type), 'nullable' => $c->Null === 'YES']; }

        $exclude = ['id', 'created_at', 'updated_at', 'deleted_at'];
        $formHtml = $bulkHtml = $tableHeaders = $tableCells = $mobileCells = "";
        $props = $edit = $rules = $save = $reset = []; $bulkProps = $bulkEdit = $bulkRules = $bulkSave = $bulkReset = [];
        $availableCols = $selectedCols = $casts = []; $hasFile = false;
        
        $cleanTableName = preg_replace('/^[a-z]+_/', '', $tableName);
        $currentModel = Str::studly(Str::singular($cleanTableName));

        foreach ($columnsData as $columnInfo) {
            $col = $columnInfo['name']; if (in_array($col, $exclude)) continue;
            $type = strtolower($columnInfo['type_name'] ?? 'varchar'); $isNullable = $columnInfo['nullable'] ?? true; $baseRule = $isNullable ? 'nullable' : 'required';
            $label = Str::headline(str_replace('_id', '', $col)); $availableCols[] = "'{$col}' => '{$label}'"; $selectedCols[] = "'{$col}'";
            $var = $col; $bulkVar = "bulkItem_" . $col;

            if ($col === 'status') { $tableHeaders .= "                    @if(in_array('{$col}', \$selectedColumns)) <th class=\"p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center\">{{ __('messages.{$col}') ?? '{$label}' }}</th> @endif\n";
            } else { $tableHeaders .= "                    @if(in_array('{$col}', \$selectedColumns)) <th wire:click=\"sortBy('{$col}')\" class=\"p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest cursor-pointer hover:text-[var(--color-primary)] transition-colors\"><div class=\"flex items-center gap-2\">{{ __('messages.{$col}') ?? '{$label}' }} @if(\$sortField === '{$col}') <svg class=\"w-3.5 h-3.5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"{{ \$sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}\"></path></svg> @endif</div></th> @endif\n"; }

            if ($col === 'status' || $type === 'tinyint' || $type === 'boolean') {
                $props[] = "public \${$var} = true;"; $bulkProps[] = "public \${$bulkVar} = true;";
                $edit[] = "\$this->{$var} = (bool) \$item->{$col};"; $bulkEdit[] = "\$this->{$bulkVar} = (bool) \$item->{$col};";
                $rules[] = "'{$var}' => '{$baseRule}|boolean',"; $bulkRules[] = "'{$bulkVar}' => '{$baseRule}|boolean',";
                $save[] = "'{$col}' => \$this->{$var},"; $bulkSave[] = "'{$col}' => \$this->{$bulkVar},";
                $reset[] = "'{$var}'"; $bulkReset[] = "'{$bulkVar}'";
                $formHtml .= $this->getStatusHtmlTemplate($col, $label, $var); $bulkHtml .= $this->getStatusHtmlTemplate($col, $label, $bulkVar);
                $tableCells .= $this->getStatusTableCell($col); $mobileCells .= $this->getStatusMobileCell($col);
                
            } elseif (str_contains($col, 'image') || str_contains($col, 'file') || str_contains($col, 'photo')) {
                $hasFile = true; $isMultiple = str_ends_with($col, 's'); if ($isMultiple) $casts[] = "'{$col}' => 'array'";
                $props[] = "public \${$var};"; $bulkProps[] = "public \${$bulkVar};";
                $edit[] = "\$this->{$var} = is_string(\$item->{$col}) ? json_decode(\$item->{$col}, true) ?? \$item->{$col} : \$item->{$col};";
                $bulkEdit[] = "\$this->{$bulkVar} = is_string(\$item->{$col}) ? json_decode(\$item->{$col}, true) ?? \$item->{$col} : \$item->{$col};";
                $rules[] = "'{$var}' => '{$baseRule}',"; $bulkRules[] = "'{$bulkVar}' => '{$baseRule}',";
                if ($isMultiple) { $save[] = "'{$col}' => empty(\$this->{$var}) ? null : collect(\$this->{$var})->map(fn(\$f) => is_string(\$f) ? \$f : \$f->store('uploads/{{modelNameLower}}', 'public'))->toJson(),"; $bulkSave[] = "'{$col}' => empty(\$this->{$bulkVar}) ? null : collect(\$this->{$bulkVar})->map(fn(\$f) => is_string(\$f) ? \$f : \$f->store('uploads/{{modelNameLower}}', 'public'))->toJson(),";
                } else { $save[] = "'{$col}' => empty(\$this->{$var}) ? null : (is_string(\$this->{$var}) ? \$this->{$var} : \$this->{$var}->store('uploads/{{modelNameLower}}', 'public')),"; $bulkSave[] = "'{$col}' => empty(\$this->{$bulkVar}) ? null : (is_string(\$this->{$bulkVar}) ? \$this->{$bulkVar} : \$this->{$bulkVar}->store('uploads/{{modelNameLower}}', 'public')),"; }
                $reset[] = "'{$var}'"; $bulkReset[] = "'{$bulkVar}'";
                $formHtml .= $this->getFileHtmlTemplate($col, $label, $var); $bulkHtml .= $this->getFileHtmlTemplate($col, $label, $bulkVar);
                $tableCells .= $this->getImageTableCell($col); $mobileCells .= $this->getImageMobileCell($col);

            } elseif (str_ends_with($col, '_id')) {
                $relation = str_replace('_id', '', $col);
                $props[] = "public \${$var};"; $bulkProps[] = "public \${$bulkVar};";
                $edit[] = "\$this->{$var} = \$item->{$col};"; $bulkEdit[] = "\$this->{$bulkVar} = \$item->{$col};";
                $rules[] = "'{$var}' => '{$baseRule}',"; $bulkRules[] = "'{$bulkVar}' => '{$baseRule}',";
                $save[] = "'{$col}' => \$this->{$var},"; $bulkSave[] = "'{$col}' => \$this->{$bulkVar},";
                $reset[] = "'{$var}'"; $bulkReset[] = "'{$bulkVar}'";
                $formHtml .= $this->getSelectHtmlTemplate($col, $label, $currentModel, $var); $bulkHtml .= $this->getSelectHtmlTemplate($col, $label, $currentModel, $bulkVar);
                $tableCells .= $this->getRelationTableCell($col, $relation); $mobileCells .= $this->getRelationMobileCell($col, $relation);
                
            } elseif (str_contains($type, 'text') || str_contains($type, 'longtext') || str_contains($type, 'mediumtext')) {
                $props[] = "public \${$var};"; $bulkProps[] = "public \${$bulkVar};";
                $edit[] = "\$this->{$var} = \$item->{$col};"; $bulkEdit[] = "\$this->{$bulkVar} = \$item->{$col};";
                $rules[] = "'{$var}' => '{$baseRule}|string',"; $bulkRules[] = "'{$bulkVar}' => '{$baseRule}|string',";
                $save[] = "'{$col}' => \$this->{$var},"; $bulkSave[] = "'{$col}' => \$this->{$bulkVar},";
                $reset[] = "'{$var}'"; $bulkReset[] = "'{$bulkVar}'";
                $formHtml .= $this->getTextAreaHtmlTemplate($col, $label, $var); $bulkHtml .= $this->getTextAreaHtmlTemplate($col, $label, $bulkVar);
                $tableCells .= $this->getTextTableCell($col); $mobileCells .= $this->getTextMobileCell($col);
            } else {
                $props[] = "public \${$var};"; $bulkProps[] = "public \${$bulkVar};";
                $edit[] = "\$this->{$var} = \$item->{$col};"; $bulkEdit[] = "\$this->{$bulkVar} = \$item->{$col};";
                $rules[] = "'{$var}' => '{$baseRule}|string|max:255',"; $bulkRules[] = "'{$bulkVar}' => '{$baseRule}|string|max:255',";
                $save[] = "'{$col}' => \$this->{$var},"; $bulkSave[] = "'{$col}' => \$this->{$bulkVar},";
                $reset[] = "'{$var}'"; $bulkReset[] = "'{$bulkVar}'";
                $formHtml .= $this->getTextHtmlTemplate($col, $label, $var); $bulkHtml .= $this->getTextHtmlTemplate($col, $label, $bulkVar);
                $tableCells .= $this->getTextTableCell($col); $mobileCells .= $this->getTextMobileCell($col);
            }
        }

        $modelCastsStr = empty($casts) ? "" : "protected \$casts = [\n        " . implode(",\n        ", $casts) . "\n    ];";
        $bulkResetStr = empty($bulkReset) ? "" : ", " . implode(", ", $bulkReset);

        return ['hasFile' => $hasFile, 'formHtml' => $formHtml, 'bulkHtml' => $bulkHtml, 'tableHeaders' => $tableHeaders, 'tableCells' => $tableCells, 'mobileCells' => $mobileCells,
            'availableColsStr' => "[" . implode(", ", $availableCols) . "]", 'selectedColsStr' => "[" . implode(", ", $selectedCols) . "]",
            'livewireProps' => implode("\n    ", $props), 'livewireEdit' => implode("\n        ", $edit),
            'livewireRules' => implode("\n            ", $rules), 'livewireSave' => implode("\n            ", $save), 'livewireReset' => implode(", ", $reset),
            'livewireBulkProps' => implode("\n    ", $bulkProps), 'livewireBulkEdit' => implode("\n            ", $bulkEdit),
            'livewireBulkRules' => implode("\n            ", $bulkRules), 'livewireBulkSave' => implode("\n            ", $bulkSave), 'livewireBulkReset' => $bulkResetStr,
            'modelCasts' => $modelCastsStr ];
    }

    private function getTextHtmlTemplate($col, $label, $var) {
        return <<<EOT
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.{$col}') ?? '{$label}' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="{$var}" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter {$label}...">
                            @error('{$var}') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ \$message }}</span> @enderror
                        </div>
                    </div>\n
EOT;
    }

    private function getTextAreaHtmlTemplate($col, $label, $var) {
        return <<<EOT
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.{$col}') ?? '{$label}' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <style> .note-modal-backdrop { z-index: 109990 !important; } .note-modal { z-index: 109991 !important; } .note-editable { background: white !important; color: black !important; min-height: 250px; } </style>
                            <div x-data="{
                                value: @entangle('{$var}'),
                                init() {
                                    let self = this;
                                    let loadDeps = function() {
                                        if (typeof jQuery === 'undefined') {
                                            let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN;
                                        } else { loadSN(); }
                                    };
                                    let loadSN = function() {
                                        if (typeof \$.fn.summernote === 'undefined') {
                                            let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css);
                                            let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self);
                                        } else { self.initSN(); }
                                    };
                                    loadDeps();
                                },
                                initSN() {
                                    let self = this; let editor = \$(this.\$refs.editor);
                                    editor.summernote({
                                        height: 300, dialogsInBody: true, placeholder: 'Enter {$label}...',
                                        toolbar: [ ['style', ['style']], ['font', ['bold', 'italic', 'underline', 'clear']], ['para', ['ul', 'ol', 'paragraph']], ['insert', ['link', 'picture', 'video']], ['view', ['fullscreen', 'codeview']] ],
                                        callbacks: {
                                            onChange: function(contents) { self.value = contents; },
                                            onImageUpload: function(files) {
                                                Array.from(files).forEach(function(file) {
                                                    let reader = new FileReader();
                                                    reader.onload = function(e) {
                                                        let pid = 'img-' + Date.now();
                                                        editor.summernote('insertImage', e.target.result, function (\$img) { \$img.attr('id', pid); \$img.css('opacity', '0.5'); });
                                                        let data = new FormData(); data.append('image', file);
                                                        let metaTag = document.querySelector('meta[name=csrf-token]');
                                                        let csrfToken = metaTag ? metaTag.content : '';
                                                        \$.ajax({
                                                            url: '/summernote-upload', method: 'POST', data: data, processData: false, contentType: false,
                                                            headers: { 'X-CSRF-TOKEN': csrfToken },
                                                            success: function(res) { let \$i = \$('#' + pid); \$i.attr('src', res.url); \$i.css('opacity', '1'); self.value = editor.summernote('code'); },
                                                            error: function(jqXHR, textStatus, errorThrown) { \$('#' + pid).remove(); alert('Upload Failed! Error Code: ' + jqXHR.status); }
                                                        });
                                                    };
                                                    reader.readAsDataURL(file);
                                                });
                                            }
                                        }
                                    });
                                    if(this.value) { editor.summernote('code', this.value); }
                                    this.\$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); });
                                }
                            }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('{$var}') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ \$message }}</span> @enderror
                        </div>
                    </div>\n
EOT;
    }

    private function getSelectHtmlTemplate($col, $label, $currentModel, $var) {
        $rel = ($col === 'parent_id') ? $currentModel : Str::studly(str_replace('_id', '', $col));
        return <<<EOT
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.{$col}') ?? '{$label}' }}</label>
                        <div class="w-full md:w-3/4">
                            <select wire:model="{$var}" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors cursor-pointer">
                                <option value="">Select {$label}...</option>
                                @php \$opts = class_exists('\App\Models\\{$rel}') ? \App\Models\\{$rel}::all() : collect(); @endphp
                                @foreach(\$opts as \$opt) <option value="{{ \$opt->id }}">{{ \$opt->name ?? \$opt->title ?? 'ID: ' . \$opt->id }}</option> @endforeach
                            </select>
                            @error('{$var}') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ \$message }}</span> @enderror
                        </div>
                    </div>\n
EOT;
    }

    private function getFileHtmlTemplate($col, $label, $var) {
        $isM = str_ends_with($col, 's'); $mAttr = $isM ? 'multiple' : '';
        return <<<EOT
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-2">{{ __('messages.{$col}') ?? '{$label}' }}</label>
                        <div class="w-full md:w-3/4">
                            <div class="flex items-center gap-3">
                                <input type="file" wire:model="{$var}" id="f-{$var}" {$mAttr} accept="image/*" class="hidden">
                                <label for="f-{$var}" class="px-4 py-2 bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white text-xs font-bold rounded-lg cursor-pointer transition-colors flex items-center gap-2 shadow-sm">Upload {$label}</label>
                            </div>
                            @if (\${$var}) <div class="mt-3 flex flex-wrap gap-3"> @if(is_array(\${$var}) || is_iterable(\${$var})) @foreach(\${$var} as \$i => \$f) <div class="relative inline-block"><img src="{{ is_string(\$f) ? asset('storage/'.\$f) : \$f->temporaryUrl() }}" class="h-20 w-24 rounded-lg border border-[var(--color-border-color)] object-cover"><button type="button" wire:click.stop="removeFile('{$var}', {{ \$i }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div> @endforeach @else <div class="relative inline-block"><img src="{{ is_string(\${$var}) ? asset('storage/'.\${$var}) : \${$var}->temporaryUrl() }}" class="h-24 w-32 rounded-lg border border-[var(--color-border-color)] object-cover"><button type="button" wire:click.stop="\$set('{$var}', null)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div> @endif </div> @endif
                        </div>
                    </div>\n
EOT;
    }

    private function getStatusHtmlTemplate($col, $label, $var) {
        return <<<EOT
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.{$col}') ?? '{$label}' }}</label>
                        <div class="w-full md:w-3/4 flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="{$var}" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-[var(--color-primary)] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                            <span class="text-sm font-bold text-[var(--color-text-main)] uppercase">{{ \${$var} ? __('messages.active') : __('messages.inactive') }}</span>
                        </div>
                    </div>\n
EOT;
    }

    // --- HTML Generators (Table & Mobile) ---
    private function getTextTableCell($col) {
        return "                        @if(in_array('{$col}', \$selectedColumns)) \n                        <td class=\"p-4 text-sm font-bold text-[var(--color-text-main)] transition-colors\">\n                            @php \$pt = strip_tags(\$item->{$col}); \$hH = strlen(\$item->{$col}) > strlen(\$pt); @endphp\n                            @if(trim(\$pt) !== '') {{ Str::limit(\$pt, 40) }} @elseif(\$hH && str_contains(\$item->{$col}, '<img')) <span class=\"text-[var(--color-primary)] text-[10px]\">Image Content</span> @else <span class=\"text-[10px] text-[var(--color-text-muted)]\">---</span> @endif\n                        </td> \n                        @endif\n";
    }
    
    private function getRelationTableCell($col, $rel) { return "                        @if(in_array('{$col}', \$selectedColumns)) <td class=\"p-4 text-sm font-bold text-[var(--color-text-main)]\">{{ \$item->{$rel}?->name ?? \$item->{$rel}?->title ?? \$item->{$col} ?? 'N/A' }}</td> @endif\n"; }
    
    private function getStatusTableCell($col) {
        return <<<EOT
                        @if(in_array('{$col}', \$selectedColumns))
                        <td class="p-4 text-center whitespace-nowrap w-[1%]">
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" wire:change.stop="toggleStatus({{ \$item->id }})" class="sr-only peer" {{ \$item->status ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-[var(--color-border-color)] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[var(--color-border-color)] after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--color-primary)] shrink-0"></div>
                            </label>
                        </td>
                        @endif\n
EOT;
    }

    private function getImageTableCell($col) {
        $isMultiple = str_ends_with($col, 's');
        if ($isMultiple) {
            return <<<EOT
                        @if(in_array('{$col}', \$selectedColumns))
                        <td class="p-4 w-[1%] whitespace-nowrap">
                            @php \$files = is_string(\$item->{$col}) ? json_decode(\$item->{$col}, true) : \$item->{$col}; @endphp
                            @if(\$files && is_array(\$files) && count(\$files) > 0)
                                <div class="flex gap-1 items-center">
                                    @foreach(array_slice(\$files, 0, 3) as \$file) 
                                        <img src="{{ asset('storage/'.\$file) }}" class="h-8 w-8 rounded-md object-cover border border-[var(--color-border-color)] shadow-sm shrink-0"> 
                                    @endforeach
                                    @if(count(\$files) > 3) <span class="text-[10px] font-bold text-[var(--color-text-muted)] bg-[var(--color-background)] px-1.5 py-0.5 rounded-md border border-[var(--color-border-color)]">+{{ count(\$files) - 3 }}</span> @endif
                                </div>
                            @else <span class="text-[10px] font-bold text-[var(--color-text-muted)]">N/A</span> @endif
                        </td>
                        @endif\n
EOT;
        } else {
            return <<<EOT
                        @if(in_array('{$col}', \$selectedColumns))
                        <td class="p-4 w-[1%] whitespace-nowrap">
                            @if(\$item->{$col}) <img src="{{ asset('storage/'.\$item->{$col}) }}" class="h-10 w-10 rounded-lg object-cover border border-[var(--color-border-color)] shadow-sm shrink-0">
                            @else <div class="h-10 w-10 rounded-lg bg-[var(--color-background)] border border-[var(--color-border-color)] flex items-center justify-center text-[10px] text-[var(--color-text-muted)] font-bold shrink-0">N/A</div> @endif
                        </td>
                        @endif\n
EOT;
        }
    }

    private function getTextMobileCell($col) { return "                    @if(in_array('{$col}', \$selectedColumns)) <div class=\"flex flex-col min-w-0\"><span class=\"text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest\">{{ __('messages.{$col}') }}</span><span class=\"text-xs font-bold text-[var(--color-text-main)] truncate block\">{{ strip_tags(\$item->{$col}) }}</span></div> @endif\n"; }
    private function getRelationMobileCell($col, $rel) { return "                    @if(in_array('{$col}', \$selectedColumns)) <div class=\"flex flex-col min-w-0\"><span class=\"text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest\">{{ __('messages.{$col}') }}</span><span class=\"text-xs font-bold text-[var(--color-text-main)] truncate block\">{{ \$item->{$rel}?->name ?? \$item->{$col} }}</span></div> @endif\n"; }
    
    private function getStatusMobileCell($col) {
        return <<<EOT
                    @if(in_array('{$col}', \$selectedColumns))
                    <div class="flex flex-col shrink-0">
                        <span class="text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.{$col}') }}</span>
                        <label class="relative inline-flex items-center cursor-pointer mt-1 shrink-0">
                            <input type="checkbox" wire:change.stop="toggleStatus({{ \$item->id }})" class="sr-only peer" {{ \$item->status ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-[var(--color-border-color)] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[var(--color-border-color)] after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--color-primary)] shrink-0"></div>
                        </label>
                    </div>
                    @endif\n
EOT;
    }

    private function getImageMobileCell($col) {
        $isMultiple = str_ends_with($col, 's');
        if ($isMultiple) {
            return <<<EOT
                    @if(in_array('{$col}', \$selectedColumns))
                    <div class="flex flex-col shrink-0">
                        <span class="text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.{$col}') }}</span>
                        @php \$files = is_string(\$item->{$col}) ? json_decode(\$item->{$col}, true) : \$item->{$col}; @endphp
                        @if(\$files && is_array(\$files) && count(\$files) > 0)
                            <div class="flex gap-1 items-center mt-1">
                                @foreach(array_slice(\$files, 0, 3) as \$file) <img src="{{ asset('storage/'.\$file) }}" class="h-8 w-8 rounded object-cover border border-[var(--color-border-color)] shadow-sm shrink-0"> @endforeach
                            </div>
                        @else <span class="text-xs font-bold text-[var(--color-text-muted)] mt-1">N/A</span> @endif
                    </div>
                    @endif\n
EOT;
        } else {
            return <<<EOT
                    @if(in_array('{$col}', \$selectedColumns))
                    <div class="flex flex-col shrink-0">
                        <span class="text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.{$col}') }}</span>
                        @if(\$item->{$col}) <img src="{{ asset('storage/'.\$item->{$col}) }}" class="h-8 w-8 rounded-lg object-cover border border-[var(--color-border-color)] shadow-sm mt-1 shrink-0"> 
                        @else <span class="text-xs font-bold text-[var(--color-text-muted)] mt-1">N/A</span> @endif
                    </div>
                    @endif\n
EOT;
        }
    }
}