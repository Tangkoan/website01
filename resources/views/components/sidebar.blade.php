@php
    $sidebarMode = Illuminate\Support\Facades\Cache::rememberForever('setting_sidebar_ui_mode', function () {
        $setting = \App\Models\Setting::where('key', 'sidebar_ui_mode')->first();
        return $setting ? $setting->value : 'hide';
    });

    $dynamicMenus = Illuminate\Support\Facades\Cache::rememberForever('sidebar_dynamic_menus', function () {
        return \App\Models\Sidebar::whereNull('parent_id')
            ->where('is_active', 1) 
            ->with(['children' => function($q) {
                $q->where('is_active', 1)->orderBy('order', 'asc');
            }])
            ->orderBy('order', 'asc')
            ->get();
    });

    // 🌟 Helper ដ៏ឆ្លាតវៃសម្រាប់បកប្រែ (ដោះស្រាយបញ្ហាអក្សរធំតូច និងពាក្យ messages. ជាន់គ្នា)
    $getLabel = function($name) {
        $key = strtolower(trim(str_replace('messages.', '', $name)));
        return trans()->has("messages.{$key}") ? __("messages.{$key}") : \Illuminate\Support\Str::headline($key);
    };
@endphp

<div>
    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 bg-gray-900/40 dark:bg-black/60 backdrop-blur-sm z-40 md:hidden" x-cloak></div>

    {{-- Main Sidebar --}}
    <aside :class="{ 'w-[260px]': !sidebarCollapsed, 'w-[80px]': sidebarCollapsed, 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }" class="fixed inset-y-0 left-0 z-99 bg-sidebar text-text-main border-r border-border-color flex flex-col h-full shadow-sm transition-all duration-300 ease-in-out md:relative md:translate-x-0">
        
        <div class="h-16 flex items-center justify-center sm:justify-start px-6 border-b border-border-color bg-sidebar shrink-0">
            <div class="flex items-center gap-3 min-w-max">
                <span class="text-2xl drop-shadow-sm transition-transform hover:scale-110">⚡</span>
                <h1 x-show="!sidebarCollapsed" x-transition.opacity class="text-lg font-bold tracking-wide text-text-main uppercase">
                    <span class="font-black">VC </span><span class="text-primary font-black">Backend</span>
                </h1>
            </div>
        </div>

        <nav class="flex-1 py-4 overflow-y-auto no-scrollbar bg-sidebar">
            @foreach($dynamicMenus as $menu)
                @php 
                    $canViewMenu = empty($menu->permission) || auth()->user()->can($menu->permission); 
                    
                    if($menu->children->isNotEmpty()) {
                        $hasVisibleChild = false;
                        foreach($menu->children as $child) {
                            if(empty($child->permission) || auth()->user()->can($child->permission) || $sidebarMode === 'disable') {
                                $hasVisibleChild = true; break;
                            }
                        }
                        if(!$hasVisibleChild) $canViewMenu = false;
                    }

                    // 🌟 ប្រើ Helper ដើម្បីបកប្រែឈ្មោះមេ (Parent)
                    $menuLabel = $getLabel($menu->name);
                @endphp

                @if($canViewMenu || $sidebarMode === 'disable')
                    @if($menu->children->isNotEmpty())
                        @php
                            $isActiveGroup = false;
                            foreach($menu->children as $child) {
                                if($child->url && request()->is($child->url . '*')) { $isActiveGroup = true; break; }
                            }
                        @endphp

                        <div x-show="!sidebarCollapsed" class="px-6 mb-2 mt-6" x-transition.opacity>
                            <p class="text-[11px] font-bold tracking-[0.15em] text-text-muted uppercase opacity-80">{{ $menuLabel }}</p>
                        </div>

                        <div x-data="{ open: {{ $isActiveGroup ? 'true' : 'false' }}, hovered: false, timeout: null }" @mouseenter="hovered = true; clearTimeout(timeout)" @mouseleave="timeout = setTimeout(() => hovered = false, 200)" class="relative">
                            <button @click="sidebarCollapsed ? null : open = !open" class="w-full group relative flex items-center justify-between px-6 py-3.5 transition-all duration-200 {{ $isActiveGroup ? 'text-primary bg-primary/10' : 'text-text-muted hover:bg-primary/5 hover:text-text-main' }}">
                                @if($isActiveGroup) <div class="absolute left-0 top-0 bottom-0 w-[5px] bg-primary rounded-r-md shadow-[2px_0_8px_var(--color-primary)] opacity-50"></div> @endif
                                <div class="flex items-center gap-4">
                                    <span class="text-[22px] transition-transform duration-300 group-hover:scale-110 flex items-center justify-center {{ $isActiveGroup ? 'drop-shadow-sm' : 'opacity-70 group-hover:opacity-100' }}">
                                        @if(!empty($menu->icon) && Str::contains($menu->icon, '<svg')) 
                                            {!! $menu->icon !!} 
                                        @elseif(!empty($menu->icon)) 
                                            <iconify-icon icon="{{ $menu->icon }}"></iconify-icon> 
                                        @else 
                                            <iconify-icon icon="healthicons:alert-circle2x-outline"></iconify-icon> 
                                        @endif
                                    </span>
                                    <span x-show="!sidebarCollapsed" class="tracking-wide whitespace-nowrap {{ $isActiveGroup ? 'font-extrabold' : 'font-semibold' }} text-text-main">{{ $menuLabel }}</span>
                                </div>
                                <div x-show="!sidebarCollapsed" class="flex items-center justify-center transition-colors">
                                    <svg :class="open ? 'rotate-180 text-primary' : 'text-text-muted group-hover:text-text-main'" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </button>
                            
                            <div x-show="open && !sidebarCollapsed" x-cloak x-collapse class="relative bg-dropdown">
                                <div class="absolute left-[34px] top-0 bottom-0 w-px bg-border-color"></div>
                                <div class="py-2 space-y-0.5">
                                    @foreach($menu->children as $child)
                                        @php 
                                            $canViewChild = empty($child->permission) || auth()->user()->can($child->permission); 
                                            // 🌟 ប្រើ Helper ដើម្បីបកប្រែឈ្មោះកូន (Child)
                                            $childLabel = $getLabel($child->name);
                                        @endphp
                                        @if($canViewChild || $sidebarMode === 'disable')
                                            <div class="pl-14 pr-6 relative">
                                                <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-border-color"></div>
                                                <x-sidebar-sub-link href="/{{ $child->url }}" :title="$childLabel" :icon="$child->icon" />
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div x-show="!sidebarCollapsed" class="px-6 mb-2 mt-6" x-transition.opacity>
                            <p class="text-[11px] font-bold tracking-[0.15em] text-text-muted uppercase opacity-80">{{ $menuLabel }}</p>
                        </div>
                        <a wire:navigate href="/{{ $menu->url }}" class="group relative flex items-center gap-4 px-6 py-3.5 transition-all duration-200 {{ request()->is($menu->url . '*') ? 'text-primary bg-primary/10' : 'text-text-muted hover:bg-primary/5 hover:text-text-main' }}">
                            @if(request()->is($menu->url . '*')) <div class="absolute left-0 top-0 bottom-0 w-[5px] bg-primary rounded-r-md shadow-[2px_0_8px_var(--color-primary)] opacity-50"></div> @endif
                            <span class="text-[22px] transition-transform duration-300 group-hover:scale-110 flex items-center justify-center {{ request()->is($menu->url . '*') ? 'drop-shadow-sm scale-110' : 'opacity-70 group-hover:opacity-100' }}">
                                @if(!empty($menu->icon) && Str::contains($menu->icon, '<svg')) 
                                    {!! $menu->icon !!} 
                                @elseif(!empty($menu->icon)) 
                                    <iconify-icon icon="{{ $menu->icon }}"></iconify-icon> 
                                @else 
                                    <iconify-icon icon="healthicons:alert-circle2x-outline"></iconify-icon> 
                                @endif
                            </span>
                            <span x-show="!sidebarCollapsed" class="tracking-wide whitespace-nowrap {{ request()->is($menu->url . '*') ? 'font-extrabold' : 'font-semibold' }} text-text-main">{{ $menuLabel }}</span>
                        </a>
                    @endif
                @endif
            @endforeach
        </nav>
    </aside>
</div>