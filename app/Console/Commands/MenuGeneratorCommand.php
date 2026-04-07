<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MenuGeneratorCommand extends Command
{
    protected $signature = 'vc:menu {model : The name of the model (e.g. Product or Settings/Category)}';
    protected $description = 'Inject a new Menu item into the Sidebar';

    public function handle()
    {
        $inputModel = $this->argument('model');
        $modelName = Str::studly(class_basename($inputModel)); 
        $modelNameLower = Str::kebab($modelName);
        $modelPath = trim(str_replace(class_basename($inputModel), '', $inputModel), '/\\');
        $classPath = empty($modelPath) ? $modelName : str_replace('/', '\\', $modelPath) . '\\' . $modelName;

        $sidebarPath = resource_path('views/components/sidebar.blade.php');
        if (!File::exists($sidebarPath)) { $this->error("❌ Error: sidebar.blade.php not found!"); return; }

        $content = File::get($sidebarPath);
        if (str_contains($content, "{{-- [MENU_START_{$classPath}] --}}")) {
            $this->warn("⚠️  Menu for {$classPath} already exists in the sidebar.");
            return;
        }

        // ✅ បង្កើត Route Link ឱ្យមាន Path ពីមុខ
        $routePrefixUrl = empty($modelPath) ? '' : strtolower(str_replace('\\', '/', $modelPath)) . '/';
        $routePath = $routePrefixUrl . Str::plural($modelNameLower); // ឧ. product/categories
        $permission = "view-{$modelNameLower}";
        
        $menuHtml = empty($modelPath) 
            ? $this->getSingleMenuHtml($classPath, $modelName, $modelNameLower, $routePath, $permission)
            : $this->getGroupMenuHtml($classPath, $modelName, $modelNameLower, $modelPath, $routePath, $permission);

        if (str_contains($content, '{{-- [DYNAMIC_MENUS_HOOK] --}}')) {
            $content = str_replace('{{-- [DYNAMIC_MENUS_HOOK] --}}', $menuHtml . "\n        {{-- [DYNAMIC_MENUS_HOOK] --}}", $content);
            File::put($sidebarPath, $content);
            $this->info("✅ Successfully injected Menu for [{$classPath}] into Sidebar! URL: /{$routePath}");
        } else {
            $this->error("❌ Error: Could not find {{-- [DYNAMIC_MENUS_HOOK] --}} in sidebar.blade.php.");
        }
    }

    private function getSingleMenuHtml($classPath, $modelName, $modelNameLower, $routePath, $permission) {
        return <<<EOT

        {{-- [MENU_START_{$classPath}] --}}
        @php \$canView{$modelName} = auth()->user()->can('{$permission}'); @endphp
        @if(\$canView{$modelName} || \$sidebarMode === 'disable')
            <div x-show="!sidebarCollapsed" class="px-6 mb-2 mt-6" x-transition.opacity>
                <p class="text-[11px] font-bold tracking-[0.15em] text-text-muted uppercase">{$modelName}</p>
            </div>
            <a wire:navigate href="/{$routePath}" 
               class="group relative flex items-center gap-4 px-6 py-3.5 transition-all duration-200 {{ request()->is('{$routePath}*') ? 'text-primary bg-primary/10' : 'text-text-muted hover:bg-primary/5 hover:text-text-main' }}">
                @if(request()->is('{$routePath}*'))
                    <div class="absolute left-0 top-0 bottom-0 w-[5px] bg-primary rounded-r-md shadow-[2px_0_8px_var(--color-primary)] opacity-50"></div>
                @endif
                <span class="text-[22px] transition-transform duration-300 group-hover:scale-110 {{ request()->is('{$routePath}*') ? 'drop-shadow-sm scale-110' : 'opacity-70 group-hover:opacity-100' }}">📦</span>
                <span x-show="!sidebarCollapsed" class="tracking-wide whitespace-nowrap {{ request()->is('{$routePath}*') ? 'font-extrabold' : 'font-semibold' }}">
                    {{ __('messages.{$modelNameLower}') ?? '{$modelName}' }}
                </span>
            </a>
        @endif
        {{-- [MENU_END_{$classPath}] --}}
EOT;
    }

    private function getGroupMenuHtml($classPath, $modelName, $modelNameLower, $modelPath, $routePath, $permission) {
        $groupTitle = strtoupper(str_replace('/', ' ', $modelPath));
        $groupPathLower = strtolower(str_replace('\\', '/', $modelPath));
        return <<<EOT

        {{-- [MENU_START_{$classPath}] --}}
        @php \$canView{$modelName} = auth()->user()->can('{$permission}'); @endphp
        @if(\$canView{$modelName} || \$sidebarMode === 'disable')
            <div x-show="!sidebarCollapsed" class="px-6 mb-2 mt-6" x-transition.opacity>
                <p class="text-[11px] font-bold tracking-[0.15em] text-text-muted uppercase">{$groupTitle}</p>
            </div>
            <div x-data="{ open: {{ request()->is('{$groupPathLower}/*') ? 'true' : 'false' }}, hovered: false, timeout: null }" @mouseenter="hovered = true; clearTimeout(timeout)" @mouseleave="timeout = setTimeout(() => hovered = false, 200)" class="relative">
                <button @click="sidebarCollapsed ? null : open = !open" class="w-full group relative flex items-center justify-between px-6 py-3.5 transition-all duration-200 {{ request()->is('{$groupPathLower}/*') ? 'text-primary bg-primary/10' : 'text-text-muted hover:bg-primary/5 hover:text-text-main' }}">
                    @if(request()->is('{$groupPathLower}/*'))
                        <div class="absolute left-0 top-0 bottom-0 w-[5px] bg-primary rounded-r-md shadow-[2px_0_8px_var(--color-primary)] opacity-50"></div>
                    @endif
                    <div class="flex items-center gap-4">
                        <span class="text-[22px] transition-transform duration-300 group-hover:scale-110 {{ request()->is('{$groupPathLower}/*') ? 'drop-shadow-sm' : 'opacity-70 group-hover:opacity-100' }}">📁</span>
                        <span x-show="!sidebarCollapsed" class="tracking-wide whitespace-nowrap {{ request()->is('{$groupPathLower}/*') ? 'font-extrabold' : 'font-semibold' }}">{{ __('messages.{$groupPathLower}') ?? '{$groupTitle}' }}</span>
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex items-center justify-center transition-colors">
                        <svg :class="open ? 'rotate-180 text-primary' : 'text-text-muted group-hover:text-text-main'" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </button>
                <div x-show="open && !sidebarCollapsed" x-cloak x-collapse class="relative bg-dropdown">
                    <div class="absolute left-[34px] top-0 bottom-0 w-px bg-border-color"></div>
                    <div class="py-2 space-y-0.5">
                        <div class="pl-14 pr-6 relative">
                            <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-border-color"></div>
                            <x-sidebar-sub-link href="/{$routePath}" :title="__('messages.{$modelNameLower}') ?? '{$modelName}'" />
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- [MENU_END_{$classPath}] --}}
EOT;
    }
}