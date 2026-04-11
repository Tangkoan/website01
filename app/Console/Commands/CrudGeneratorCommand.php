<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrudGeneratorCommand extends Command
{
    protected $signature = 'vc:crud {model : The name of the model (e.g. Product or Settings/Category)}';
    protected $description = 'Generate Smart CRUD with Dynamic Field Support';

    protected $overwriteMode = 'all';

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

        $modelDest = app_path("Models/{$classPath}.php");
        $livewireClassDest = empty($modelPath) ? app_path("Livewire/{$modelName}Management.php") : app_path("Livewire/" . str_replace('\\', '/', $classPath) . "Management.php");
        $viewDir = resource_path("views/livewire/{$viewPathBase}");
        $mainViewDest = "{$viewDir}/{$modelNameLower}-management.blade.php";

        // 🌟 មុខងារ UX ថ្មី៖ បង្ហាញ Menu ជម្រើសទាំង ៣
        if (File::exists($modelDest) || File::exists($livewireClassDest) || File::exists($mainViewDest)) {
            $this->warn("\n⚠️  WARNING: CRUD files for [{$classPath}] already exist!");
            
            $choice = $this->choice(
                'Please Select your option!',
                [
                    'Overwrite All',
                    'Cancel',
                    'Ask File by File'
                ],
                1 // Default គឺយកលេខ 2 (Cancel) ដើម្បីសុវត្ថិភាព
            );

            if (str_starts_with($choice, '2')) {
                $this->info("🛑 Operation canceled. Your files are safe.");
                return;
            } elseif (str_starts_with($choice, '1')) {
                $this->overwriteMode = 'all';
                $this->info("♻️  Proceeding to overwrite ALL files...");
            } else {
                $this->overwriteMode = 'ask';
                $this->info("🧐 We will ask you before overwriting each existing file.");
            }
        } else {
            $this->overwriteMode = 'all';
        }

        $this->info("🚀 Starting Smart CRUD Generation for [{$classPath}]...");

        $tableName = $this->discoverTableName($baseTableName);
        if (!$tableName) { $this->error("❌ Table not found!"); return; }

        $dynamicData = $this->generateDynamicFields($tableName);

        // 1. Generate Model
        $this->generateFile('model.stub', $modelDest, [
            '{{ModelName}}' => $modelName, 
            '{{TableName}}' => $baseTableName, 
            '{{ModelCasts}}' => $dynamicData['modelCasts']
        ]);
        
        // 2. Generate Service
        $this->generateFile('service.stub', app_path("Services/{$modelName}Service.php"), [
            '{{ModelName}}' => $modelName, '{{EagerLoads}}' => $dynamicData['eagerLoadsStr']
        ]);

        // 3. Generate Livewire
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

        // 4. Generate Views
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

        $this->generateFile('view-main.stub', $mainViewDest, $replacements);
        $this->generateFile('view-header.stub', "{$partialsDir}/header.blade.php", $replacements);
        $this->generateFile('view-filters.stub', "{$partialsDir}/filters.blade.php", $replacements);
        $this->generateFile('view-table.stub', "{$partialsDir}/table.blade.php", $replacements);
        $this->generateFile('view-cards-mobile.stub', "{$partialsDir}/cards-mobile.blade.php", $replacements);
        $this->generateFile('view-modal-form.stub', "{$partialsDir}/modal-form.blade.php", $replacements);
        $this->generateFile('view-modal-bulk-edit.stub', "{$partialsDir}/modal-bulk-edit.blade.php", $replacements);

        $this->appendRoute($modelName, $modelPath, $classPath);
        $this->injectIntoGenericControllers($modelName, $modelNameLower, $modelPath);

        $this->info("✅ CRUD [{$modelName}] generated with Smart Logic!");
    }

    protected function discoverTableName($baseName) {
        $tables = array_map(fn($t) => current((array)$t), DB::select('SHOW TABLES'));
        foreach ($tables as $table) { if ($table === $baseName || Str::endsWith($table, '_' . $baseName)) return $table; }
        return null;
    }

    private function generateDynamicFields($tableName) {
        $cols = DB::select('SHOW COLUMNS FROM ' . $tableName);
        $exclude = ['id', 'created_at', 'updated_at', 'deleted_at'];
        $formHtml = $bulkHtml = $tableHeaders = $tableCells = $mobileCells = "";
        $props = $edit = $rules = $save = $reset = $bulkProps = $bulkEdit = $bulkRules = $bulkSave = $bulkReset = [];
        $availableCols = $selectedCols = $casts = $eagerLoads = []; $hasFile = false;

        $cleanTableName = preg_replace('/^[a-z]+_/', '', $tableName);
        $currentModel = Str::studly(Str::singular($cleanTableName));

        foreach ($cols as $c) {
            $col = $c->Field; if (in_array($col, $exclude)) continue;
            $type = strtolower($c->Type); $isNullable = $c->Null === 'YES';
            $label = Str::headline(str_replace('_id', '', $col));
            $availableCols[] = "'{$col}' => '{$label}'"; $selectedCols[] = "'{$col}'";
            $var = $col; $bulkVar = "bulkItem_" . $col;
            $baseRule = $isNullable ? 'nullable' : 'required';

            // Header Logic
            $tableHeaders .= "                    @if(in_array('{$col}', \$selectedColumns)) <th wire:click=\"sortBy('{$col}')\" class=\"p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest cursor-pointer hover:text-[var(--color-primary)] transition-colors\"><div class=\"flex items-center gap-2\">{{ __('messages.{$col}') ?? '{$label}' }} @if(\$sortField === '{$col}') <svg class=\"w-3.5 h-3.5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"{{ \$sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}\"></path></svg> @endif</div></th> @endif\n";

            // 1. SMART DETECTION: Boolean / Status / Is_
            if (str_contains($type, 'tinyint(1)') || $type === 'boolean' || $col === 'status' || str_starts_with($col, 'is_')) {
                $props[] = "public \${$var} = true;"; $bulkProps[] = "public \${$bulkVar} = true;";
                $edit[] = "\$this->{$var} = (bool) \$item->{$col};"; $bulkEdit[] = "\$this->{$bulkVar} = (bool) \$item->{$col};";
                $rules[] = "'{$var}' => 'nullable|boolean',"; $bulkRules[] = "'{$bulkVar}' => 'nullable|boolean',";
                $save[] = "'{$col}' => \$this->{$var},"; $bulkSave[] = "'{$col}' => \$this->{$bulkVar},";
                $reset[] = "'{$var}'"; $bulkReset[] = "'{$bulkVar}'";
                $formHtml .= $this->getStatusHtmlTemplate($col, $label, $var); $bulkHtml .= $this->getStatusHtmlTemplate($col, $label, $bulkVar);
                $tableCells .= $this->getStatusTableCell($col); $mobileCells .= $this->getStatusMobileCell($col);
                
            // 2. SMART DETECTION: Images / Files
            } elseif (str_contains($col, 'image') || str_contains($col, 'file')) {
                $hasFile = true; $isM = str_ends_with($col, 's'); if ($isM) $casts[] = "'{$col}' => 'array'";
                $props[] = "public \${$var};"; $bulkProps[] = "public \${$bulkVar};";
                $edit[] = "\$this->{$var} = is_string(\$item->{$col}) ? json_decode(\$item->{$col}, true) ?? \$item->{$col} : \$item->{$col};";
                $bulkEdit[] = "\$this->{$bulkVar} = is_string(\$item->{$col}) ? json_decode(\$item->{$col}, true) ?? \$item->{$col} : \$item->{$col};";
                $rules[] = "'{$var}' => 'nullable',"; $bulkRules[] = "'{$bulkVar}' => 'nullable',";
                $save[] = $isM ? "'{$col}' => empty(\$this->{$var}) ? null : collect(\$this->{$var})->map(fn(\$f) => is_string(\$f) ? \$f : \$f->store('uploads/{{modelNameLower}}', 'public'))->toJson()," : "'{$col}' => empty(\$this->{$var}) ? null : (is_string(\$this->{$var}) ? \$this->{$var} : \$this->{$var}->store('uploads/{{modelNameLower}}', 'public')),";
                $bulkSave[] = $isM ? "'{$col}' => empty(\$this->{$bulkVar}) ? null : collect(\$this->{$bulkVar})->map(fn(\$f) => is_string(\$f) ? \$f : \$f->store('uploads/{{modelNameLower}}', 'public'))->toJson()," : "'{$col}' => empty(\$this->{$bulkVar}) ? null : (is_string(\$this->{$bulkVar}) ? \$this->{$bulkVar} : \$this->{$bulkVar}->store('uploads/{{modelNameLower}}', 'public')),";
                $reset[] = "'{$var}'"; $bulkReset[] = "'{$bulkVar}'";
                $formHtml .= $this->getFileHtmlTemplate($col, $label, $var); $bulkHtml .= $this->getFileHtmlTemplate($col, $label, $bulkVar);
                $tableCells .= $this->getImageTableCell($col); $mobileCells .= $this->getImageMobileCell($col);

            // 3. SMART DETECTION: Relationships (_id)
            } elseif (str_ends_with($col, '_id')) {
                $rel = str_replace('_id', '', $col); $eagerLoads[] = "'{$rel}'";
                $props[] = "public \${$var};"; $bulkProps[] = "public \${$bulkVar};";
                $edit[] = "\$this->{$var} = \$item->{$col};"; $bulkEdit[] = "\$this->{$bulkVar} = \$item->{$col};";
                $rules[] = "'{$var}' => 'required',"; $bulkRules[] = "'{$bulkVar}' => 'required',";
                $save[] = "'{$col}' => \$this->{$var},"; $bulkSave[] = "'{$col}' => \$this->{$bulkVar},";
                $reset[] = "'{$var}'"; $bulkReset[] = "'{$bulkVar}'";
                $formHtml .= $this->getSelectHtmlTemplate($col, $label, $currentModel, $var); $bulkHtml .= $this->getSelectHtmlTemplate($col, $label, $currentModel, $bulkVar);
                $tableCells .= $this->getRelationTableCell($col, $rel); $mobileCells .= $this->getRelationMobileCell($col, $rel);

            // 4. SMART DETECTION: Long Text (Summernote)
            } elseif (str_contains($type, 'text')) {
                $props[] = "public \${$var};"; $bulkProps[] = "public \${$bulkVar};";
                $edit[] = "\$this->{$var} = \$item->{$col};"; $bulkEdit[] = "\$this->{$bulkVar} = \$item->{$col};";
                $rules[] = "'{$var}' => 'nullable|string',"; $bulkRules[] = "'{$bulkVar}' => 'nullable|string',";
                $save[] = "'{$col}' => \$this->{$var},"; $bulkSave[] = "'{$col}' => \$this->{$bulkVar},";
                $reset[] = "'{$var}'"; $bulkReset[] = "'{$bulkVar}'";
                $formHtml .= $this->getTextAreaHtmlTemplate($col, $label, $var); $bulkHtml .= $this->getTextAreaHtmlTemplate($col, $label, $bulkVar);
                $tableCells .= $this->getTextTableCell($col); $mobileCells .= $this->getTextMobileCell($col);

            // 5. ✅ NEW: SMART DETECTION: Numbers (int, decimal, float)
            } elseif (preg_match('/(int|decimal|float|double|numeric)/', $type)) {
                $props[] = "public \${$var};"; $bulkProps[] = "public \${$bulkVar};";
                $edit[] = "\$this->{$var} = \$item->{$col};"; $bulkEdit[] = "\$this->{$bulkVar} = \$item->{$col};";
                
                // បើសិនជាប្រភេទ int ឱ្យ Rule ជា integer បើ decimal ឱ្យ Rule ជា numeric
                $numRule = str_contains($type, 'int') ? 'integer' : 'numeric';
                $rules[] = "'{$var}' => '{$baseRule}|{$numRule}',"; 
                $bulkRules[] = "'{$bulkVar}' => '{$baseRule}|{$numRule}',";
                
                $save[] = "'{$col}' => \$this->{$var},"; $bulkSave[] = "'{$col}' => \$this->{$bulkVar},";
                $reset[] = "'{$var}'"; $bulkReset[] = "'{$bulkVar}'";
                $formHtml .= $this->getTextHtmlTemplate($col, $label, $var); $bulkHtml .= $this->getTextHtmlTemplate($col, $label, $bulkVar);
                $tableCells .= $this->getTextTableCell($col); $mobileCells .= $this->getTextMobileCell($col);

            // 6. DEFAULT: Strings / Varchar
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

        return [
            'hasFile' => $hasFile, 'formHtml' => $formHtml, 'bulkHtml' => $bulkHtml, 'tableHeaders' => $tableHeaders, 'tableCells' => $tableCells, 'mobileCells' => $mobileCells,
            'availableColsStr' => "[" . implode(", ", $availableCols) . "]", 'selectedColsStr' => "[" . implode(", ", $selectedCols) . "]",
            'livewireProps' => implode("\n    ", $props), 'livewireEdit' => implode("\n        ", $edit), 'livewireRules' => implode("\n            ", $rules), 'livewireSave' => implode("\n            ", $save), 'livewireReset' => implode(", ", $reset),
            'livewireBulkProps' => implode("\n    ", $bulkProps), 'livewireBulkEdit' => implode("\n            ", $bulkEdit), 'livewireBulkRules' => implode("\n            ", $bulkRules), 'livewireBulkSave' => implode("\n            ", $bulkSave), 'livewireBulkReset' => ", " . implode(", ", $bulkReset),
            'modelCasts' => empty($casts) ? "" : "protected \$casts = [" . implode(", ", $casts) . "];",
            'eagerLoadsStr' => empty($eagerLoads) ? "" : "->with([" . implode(", ", $eagerLoads) . "])"
        ];
    }

    // --- TEMPLATES (Updated for Smart Logic) ---
    private function getStatusTableCell($col) {
        return <<<EOT
                        @if(in_array('{$col}', \$selectedColumns))
                        <td class="p-4 text-center whitespace-nowrap w-[1%]">
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" wire:change.stop="toggleField({{ \$item->id }}, '{$col}')" class="sr-only peer" {{ \$item->{$col} ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-[var(--color-border-color)] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[var(--color-border-color)] after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--color-primary)] shrink-0"></div>
                            </label>
                        </td>
                        @endif\n
EOT;
    }

    private function getStatusMobileCell($col) {
        return <<<EOT
                    @if(in_array('{$col}', \$selectedColumns))
                    <div class="flex flex-col shrink-0">
                        <span class="text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.{$col}') }}</span>
                        <label class="relative inline-flex items-center cursor-pointer mt-1 shrink-0">
                            <input type="checkbox" wire:change.stop="toggleField({{ \$item->id }}, '{$col}')" class="sr-only peer" {{ \$item->{$col} ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-[var(--color-border-color)] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[var(--color-border-color)] after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--color-primary)] shrink-0"></div>
                        </label>
                    </div>
                    @endif\n
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

    private function getTextTableCell($col) {
        return "                        @if(in_array('{$col}', \$selectedColumns)) \n                        <td class=\"p-4 text-sm font-bold text-[var(--color-text-main)] transition-colors\">\n                            @php \$pt = strip_tags(\$item->{$col}); @endphp\n                            {{ Str::limit(\$pt, 40) ?: '---' }}\n                        </td> \n                        @endif\n";
    }

    private function getRelationTableCell($col, $rel) { return "                        @if(in_array('{$col}', \$selectedColumns)) <td class=\"p-4 text-sm font-bold text-[var(--color-text-main)]\">{{ \$item->{$rel}?->name ?? \$item->{$rel}?->title ?? \$item->{$col} ?? 'N/A' }}</td> @endif\n"; }

    private function getImageTableCell($col) {
        $isM = str_ends_with($col, 's');
        return <<<EOT
                        @if(in_array('{$col}', \$selectedColumns))
                        <td class="p-4 w-[1%] whitespace-nowrap">
                            @php \$f = is_string(\$item->{$col}) ? json_decode(\$item->{$col}, true) : \$item->{$col}; @endphp
                            @if(\$f) <img src="{{ asset('storage/'.(is_array(\$f) ? \$f[0] : \$f)) }}" class="h-10 w-10 rounded-lg object-cover border border-[var(--color-border-color)] shrink-0">
                            @else <span class="text-[10px] text-[var(--color-text-muted)]">N/A</span> @endif
                        </td>
                        @endif\n
EOT;
    }

    private function getTextMobileCell($col) { return "                    @if(in_array('{$col}', \$selectedColumns)) <div class=\"flex flex-col min-w-0\"><span class=\"text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest\">{{ __('messages.{$col}') }}</span><span class=\"text-xs font-bold text-[var(--color-text-main)] truncate block\">{{ strip_tags(\$item->{$col}) }}</span></div> @endif\n"; }
    private function getRelationMobileCell($col, $rel) { return "                    @if(in_array('{$col}', \$selectedColumns)) <div class=\"flex flex-col min-w-0\"><span class=\"text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest\">{{ __('messages.{$col}') }}</span><span class=\"text-xs font-bold text-[var(--color-text-main)] truncate block\">{{ \$item->{$rel}?->name ?? \$item->{$col} }}</span></div> @endif\n"; }
    private function getImageMobileCell($col) { return "                    @if(in_array('{$col}', \$selectedColumns)) <div class=\"flex flex-col shrink-0\"><span class=\"text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest\">{{ __('messages.{$col}') }}</span><img src=\"{{ \$item->{$col} ? asset('storage/'.(is_array(json_decode(\$item->{$col},true))?json_decode(\$item->{$col},true)[0]:\$item->{$col})) : '' }}\" class=\"h-8 w-8 rounded-lg mt-1 shrink-0\"></div> @endif\n"; }

    private function getTextHtmlTemplate($col, $label, $var) {
        return <<<EOT
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.{$col}') ?? '{$label}' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="text" wire:model="{$var}" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter {$label}...">
                            @error('{$var}') <span class="text-red-500 text-xs mt-1 block">{{ \$message }}</span> @enderror
                        </div>
                    </div>\n
EOT;
    }

    private function getTextAreaHtmlTemplate($col, $label, $var) {
        return <<<EOT
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.{$col}') ?? '{$label}' }}</label>
                        <div class="w-full md:w-3/4" wire:ignore>
                            <div x-data="{ value: @entangle('{$var}'), init() { let self = this; let loadDeps = function() { if (typeof jQuery === 'undefined') { let jq = document.createElement('script'); jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js'; document.head.appendChild(jq); jq.onload = loadSN; } else { loadSN(); } }; let loadSN = function() { if (typeof \$.fn.summernote === 'undefined') { let css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css'; document.head.appendChild(css); let sn = document.createElement('script'); sn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js'; document.head.appendChild(sn); sn.onload = self.initSN.bind(self); } else { self.initSN(); } }; loadDeps(); }, initSN() { let self = this; let editor = \$(this.\$refs.editor); editor.summernote({ height: 200, dialogsInBody: true, callbacks: { onChange: function(contents) { self.value = contents; } } }); if(this.value) editor.summernote('code', this.value); this.\$watch('value', function(nv) { if (nv !== editor.summernote('code')) editor.summernote('code', nv || ''); }); } }">
                                <textarea x-ref="editor" class="hidden"></textarea>
                            </div>
                            @error('{$var}') <span class="text-red-500 text-xs mt-1 block">{{ \$message }}</span> @enderror
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
                            <select wire:model="{$var}" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none cursor-pointer">
                                <option value="">Select {$label}...</option>
                                @php \$opts = class_exists('\App\Models\\{$rel}') ? \App\Models\\{$rel}::limit(100)->get() : collect(); @endphp
                                @foreach(\$opts as \$opt) <option value="{{ \$opt->id }}">{{ \$opt->name ?? \$opt->title ?? 'ID: ' . \$opt->id }}</option> @endforeach
                            </select>
                        </div>
                    </div>\n
EOT;
    }

    private function getFileHtmlTemplate($col, $label, $var) {
        $isM = str_ends_with($col, 's'); $mAttr = $isM ? 'multiple' : '';
        return <<<EOT
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] border-dashed mb-4 last:mb-0">
                        <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.{$col}') ?? '{$label}' }}</label>
                        <div class="w-full md:w-3/4">
                            <input type="file" wire:model="{$var}" id="f-{$var}" {$mAttr} accept="image/*" class="hidden">
                            <label for="f-{$var}" class="px-4 py-2 bg-[var(--color-primary)] text-white text-xs font-bold rounded-lg cursor-pointer inline-block">Upload {$label}</label>
                            @if (\${$var}) <div class="mt-3 flex flex-wrap gap-2"> @if(is_array(\${$var}) || is_iterable(\${$var})) @foreach(\${$var} as \$i => \$f) <img src="{{ is_string(\$f) ? asset('storage/'.\$f) : \$f->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> @endforeach @else <img src="{{ is_string(\${$var}) ? asset('storage/'.\${$var}) : \${$var}->temporaryUrl() }}" class="h-20 w-20 rounded-lg object-cover"> @endif </div> @endif
                        </div>
                    </div>\n
EOT;
    }

    protected function generateFile($stubName, $destinationPath, $replacements) {
        $fileName = basename($destinationPath);

        // 🌟 ឆែកមើលបើ File ហ្នឹងមានរួចហើយ និងអ្នកប្រើប្រាស់រើសជម្រើសទី ៣ (Ask)
        if (File::exists($destinationPath)) {
            if ($this->overwriteMode === 'ask') {
                if (!$this->confirm("File [{$fileName}] already exists. Overwrite?", false)) {
                    $this->line("  ⏭️  Skipped: {$fileName}");
                    return; // រំលង File នេះ មិនបញ្ចេញកូដថ្មីទេ
                }
            }
        }

        $stubPath = base_path("stubs/{$stubName}");
        $content = str_replace(array_keys($replacements), array_values($replacements), File::get($stubPath));
        File::ensureDirectoryExists(dirname($destinationPath), 0755, true);
        File::put($destinationPath, $content);
        
        // លោតសារប្រាប់ថាបានបង្កើត File អ្វីខ្លះ
        $this->line("  ✨ Generated: {$fileName}"); 
    }

    protected function appendRoute($modelName, $modelPath, $classPath) {
        $webPhpPath = base_path('routes/web.php');
        $content = File::get($webPhpPath);
        $className = "{$modelName}Management";
        $fullClassName = "App\\Livewire\\" . ($modelPath ? str_replace('/', '\\', $modelPath) . '\\' : '') . $className;
        
        if (!str_contains($content, "use {$fullClassName};")) {
            $content = preg_replace('/<\?php\s+/', "<?php\n\nuse {$fullClassName};", $content, 1);
        }

        $modelNameLower = Str::kebab($modelName); 
        $routeSlug = Str::plural($modelNameLower); 
        $permission = "view-{$modelNameLower}";
        
        // ✅ កំណត់ឈ្មោះ Route ពេញលេញ (ឧ. settings.sidebars.index)
        $fullRouteName = (empty($modelPath) ? '' : strtolower(str_replace(['\\', '/'], '.', $modelPath)) . '.') . $routeSlug . '.index';
        
        $authPattern = "/(Route::middleware\(\s*\[?\s*['\"]auth['\"]\s*\]?\s*\)->group\(\s*function\s*\(\)\s*\{)/is";

        if (empty($modelPath)) {
            $routeCode = "    Route::get('/{$routeSlug}', {$className}::class)->name('{$fullRouteName}')->can('{$permission}');";
            if (!str_contains($content, "{$className}::class")) {
                $content = preg_replace($authPattern, "$1\n{$routeCode}", $content, 1);
            }
        } else {
            $prefix = strtolower(str_replace('\\', '/', $modelPath)); 
            $prefixName = str_replace('/', '.', $prefix); 
            
            // ✅ បង្កើតកូដ Route ដែលប្រើឈ្មោះពេញ ដើម្បីការពារ Error ជាមួយ Trash Page
            $innerRoute = "        Route::get('/{$routeSlug}', {$className}::class)->name('{$fullRouteName}')->can('{$permission}');";
            
            $groupPattern = "/(Route::prefix\(['\"]{$prefix}['\"]\).*?->group\(\s*function\s*\(\)\s*\{)/is";
            
            if (preg_match($groupPattern, $content)) { 
                if (!str_contains($content, "{$className}::class")) {
                    $content = preg_replace($groupPattern, "$1\n{$innerRoute}", $content, 1);
                }
            } else { 
                // បើបង្កើត Group ថ្មី គឺដាក់ទាំង Prefix និង Name ឱ្យមានស្តង់ដារតែម្តង
                $groupCode = "\n    Route::prefix('{$prefix}')->name('{$prefixName}.')->group(function () {\n        Route::get('/{$routeSlug}', {$className}::class)->name('{$routeSlug}.index')->can('{$permission}');\n    });\n";
                if (!str_contains($content, "{$className}::class")) {
                    $content = preg_replace($authPattern, "$1\n{$groupCode}", $content, 1);
                }
            }
        }
        File::put($webPhpPath, $content);
    }

    protected function injectIntoGenericControllers($modelName, $modelNameLower, $modelPath) {
        $logPath = app_path('Livewire/Settings/GenericLog.php');
        $trashPath = app_path('Livewire/Settings/GenericTrash.php');

        // 🌟 ១. កំណត់ Full Model Path ឱ្យត្រូវ ទោះបីជាមាន Folder ក៏ដោយ (ឧ. Product/Category)
        $fullModelPath = empty($modelPath) ? "\App\Models\\{$modelName}::class" : "\App\Models\\" . str_replace('/', '\\', $modelPath) . "\\{$modelName}::class";
        $mapEntry = "\n            '{$modelNameLower}' => {$fullModelPath},";
        
        // 🌟 ២. បង្កើតឈ្មោះ Route ឱ្យត្រូវតាមស្តង់ដារ (រក្សា Logic ចាស់ដដែល)
        $routePrefixName = empty($modelPath) ? '' : strtolower(str_replace(['\\', '/'], '.', $modelPath)) . '.';
        $pluralModelRoute = $routePrefixName . Str::plural($modelNameLower) . '.index'; 
        
        $backRouteEntry = "\n            '{$modelNameLower}' => '{$pluralModelRoute}',";

        foreach ([$logPath, $trashPath] as $path) {
            if (File::exists($path)) {
                $content = File::get($path);
                $isModified = false; // បង្កើតអថេរសម្រាប់ឆែកមើលថាតើកូដមានការផ្លាស់ប្តូរឬអត់

                // 🌟 ៣. ឆែក Model Map ឱ្យច្បាស់លាស់ និងប្រើ Regex ដែលមានសុវត្ថិភាព
                if (!str_contains($content, "'{$modelNameLower}' => {$fullModelPath}")) {
                    $newContent = preg_replace('/(getModelMap\(\)\s*\{.*?return\s*\[)(.*?)(\s*\];\s*\})/s', "$1$2$mapEntry$3", $content);
                    // ការពារកុំឱ្យវា Replace ចេញ null បើសិនជា Regex Error
                    if ($newContent !== null && $newContent !== $content) {
                        $content = $newContent;
                        $isModified = true;
                    }
                }
                
                // 🌟 ៤. ឆែក Route Map ឱ្យច្បាស់លាស់ និងប្រើ Regex ដែលមានសុវត្ថិភាព
                if (!str_contains($content, "'{$modelNameLower}' => '{$pluralModelRoute}'")) {
                    $newContent = preg_replace('/(\$backRouteMap\s*=\s*\[)(.*?)(\s*\];)/s', "$1$2$backRouteEntry$3", $content);
                    if ($newContent !== null && $newContent !== $content) {
                        $content = $newContent;
                        $isModified = true;
                    }
                }
                
                // 🌟 ៥. Save ចូល File វិញតែពេលមានការផ្លាស់ប្តូរប៉ុណ្ណោះ (ចំណេញ Performance)
                if ($isModified) {
                    File::put($path, $content);
                    // បញ្ចេញសារប្រាប់ក្នុង Terminal បើសិនជា Command ដើរ
                    if (method_exists($this, 'info')) {
                        $this->info("    🔗 Injected [{$modelName}] routes successfully into " . basename($path));
                    }
                }
            }
        }
    }
}