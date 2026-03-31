<div>
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 bg-gray-900/40 dark:bg-black/60 backdrop-blur-sm z-20 md:hidden" x-cloak></div>

<aside 
    :class="{ 'w-[260px]': !sidebarCollapsed, 'w-[80px]': sidebarCollapsed, 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
    class="fixed inset-y-0 left-0 z-30 bg-sidebar text-text-main border-r border-border-color flex flex-col h-full shadow-sm transition-all duration-300 ease-in-out md:relative md:translate-x-0"
>
    <div class="h-16 flex items-center justify-center sm:justify-start px-6 border-b border-border-color bg-sidebar">
        <div class="flex items-center gap-3 min-w-max">
            <span class="text-2xl drop-shadow-sm transition-transform hover:scale-110">⚡</span>
            <h1 x-show="!sidebarCollapsed" x-transition.opacity class="text-lg font-bold tracking-wide text-text-main uppercase">
                <span class=" font-black">Nealika </span><span class="text-primary font-black">Backend</span>
            </h1>
        </div>
    </div>

    <nav class="flex-1 py-4 overflow-y-auto no-scrollbar bg-sidebar">
        
        {{-- ============================== --}}
        {{-- MENU: DASHBOARD                --}}
        {{-- ============================== --}}
        @can('view_dashboard')
            <div x-show="!sidebarCollapsed" class="px-6 mb-2 mt-2" x-transition.opacity>
                <p class="text-[11px] font-bold tracking-[0.15em] text-text-muted uppercase">Main Menu</p>
            </div>

            <a wire:navigate href="/dashboard" 
               class="group relative flex items-center gap-4 px-6 py-3.5 transition-all duration-200
                      {{ request()->is('dashboard') 
                          ? 'text-primary bg-primary/10' 
                          : 'text-text-muted hover:bg-primary/5 hover:text-text-main' }}">
                
                @if(request()->is('dashboard'))
                    <div class="absolute left-0 top-0 bottom-0 w-[5px] bg-primary rounded-r-md shadow-[2px_0_8px_var(--color-primary)] opacity-50"></div>
                @endif

                <span class="text-[22px] transition-transform duration-300 group-hover:scale-110 {{ request()->is('dashboard') ? 'drop-shadow-sm scale-110' : 'opacity-70 group-hover:opacity-100' }}">
                    🏠
                </span>
                
                <span x-show="!sidebarCollapsed" class="tracking-wide whitespace-nowrap {{ request()->is('dashboard') ? 'font-extrabold' : 'font-semibold' }}">
                    {{ __('messages.home') ?? 'Dashboard' }}
                </span>
            </a>
        @endcan

        {{-- ============================== --}}
        {{-- MENU GROUP: SETTINGS           --}}
        {{-- ============================== --}}
        {{-- ប្រើ @canany ដើម្បីបង្ហាញ Group នេះ លុះត្រាតែគាត់មានសិទ្ធិយ៉ាងហោចណាស់១ ក្នុងចំណោមសិទ្ធិទាំងនេះ --}}
        @canany(['view_shop_info', 'view_permissions', 'view_roles', 'view_theme', 'view_users'])
            <div x-show="!sidebarCollapsed" class="px-6 mb-2 mt-6" x-transition.opacity>
                <p class="text-[11px] font-bold tracking-[0.15em] text-text-muted uppercase">Management</p>
            </div>

            <div x-data="{ 
                    open: {{ request()->is('settings*') ? 'true' : 'false' }}, 
                    hovered: false,
                    timeout: null 
                 }" 
                 @mouseenter="hovered = true; clearTimeout(timeout)" 
                 @mouseleave="timeout = setTimeout(() => hovered = false, 200)"
                 class="relative">
                
                {{-- Main Settings Button --}}
                <button @click="sidebarCollapsed ? null : open = !open" 
                    class="w-full group relative flex items-center justify-between px-6 py-3.5 transition-all duration-200
                           {{ request()->is('settings*') 
                               ? 'text-primary bg-primary/10' 
                               : 'text-text-muted hover:bg-primary/5 hover:text-text-main' }}">
                    
                    @if(request()->is('settings*'))
                        <div class="absolute left-0 top-0 bottom-0 w-[5px] bg-primary rounded-r-md shadow-[2px_0_8px_var(--color-primary)] opacity-50"></div>
                    @endif

                    <div class="flex items-center gap-4">
                        <span class="text-[22px] transition-transform duration-300 group-hover:rotate-45 {{ request()->is('settings*') ? 'drop-shadow-sm' : 'opacity-70 group-hover:opacity-100' }}">
                            ⚙️
                        </span>
                        <span x-show="!sidebarCollapsed" class="tracking-wide whitespace-nowrap {{ request()->is('settings*') ? 'font-extrabold' : 'font-semibold' }}">
                            {{ __('messages.settings') ?? 'Settings' }}
                        </span>
                    </div>
                    
                    <div x-show="!sidebarCollapsed" class="flex items-center justify-center transition-colors">
                        <svg :class="open ? 'rotate-180 text-primary' : 'text-text-muted group-hover:text-text-main'" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                {{-- Expanded Sub-menu (ពេលមិនទាន់ Collapse) --}}
                <div x-show="open && !sidebarCollapsed" x-cloak x-collapse class="relative bg-dropdown">
                    <div class="absolute left-[34px] top-0 bottom-0 w-px bg-border-color"></div>

                    <div class="py-2 space-y-0.5">
                        @can('view_shop_info')
                            <div class="pl-14 pr-6 relative">
                                <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-border-color"></div>
                                <x-sidebar-sub-link href="/settings/shop" :title="__('messages.shop_info') ?? 'Shop Info'" />
                            </div>
                        @endcan
                        
                        @can('view_permissions')
                            <div class="pl-14 pr-6 relative">
                                <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-border-color"></div>
                                <x-sidebar-sub-link href="/settings/permission" :title="__('messages.permission') ?? 'Permission'" />
                            </div>
                        @endcan

                        @can('view_roles')
                            <div class="pl-14 pr-6 relative">
                                <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-border-color"></div>
                                <x-sidebar-sub-link href="/settings/roles" :title="__('messages.roles') ?? 'Roles'" />
                            </div>
                        @endcan

                        @can('view_theme')
                            <div class="pl-14 pr-6 relative">
                                <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-border-color"></div>
                                <x-sidebar-sub-link href="/settings/theme" :title="__('messages.theme') ?? 'Theme Styling'" />
                            </div>
                        @endcan

                        @can('view_users')
                            <div class="pl-14 pr-6 relative">
                                <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-border-color"></div>
                                <x-sidebar-sub-link href="/settings/users" :title="__('messages.users') ?? 'User Management'" />
                            </div>
                        @endcan
                    </div>
                </div>

                {{-- Hover Sub-menu (ពេល Sidebar Collapsed បង្រួមតូច) --}}
                <div x-show="hovered && sidebarCollapsed" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-x-2"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-x-2"
                     @mouseenter="hovered = true; clearTimeout(timeout)"
                     @mouseleave="hovered = false"
                     class="fixed left-[80px] top-auto ml-1 w-60 z-50 pointer-events-auto"
                     x-cloak>
                     
                    <div class="absolute -left-4 top-0 w-4 h-full"></div>

                    <div class="bg-dropdown border border-border-color shadow-xl rounded-xl overflow-hidden relative">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-primary"></div>
                        
                        <div class="px-5 py-3.5 border-b border-border-color bg-primary/5 mt-1">
                            <span class="text-xs font-bold uppercase tracking-widest text-primary">
                                {{ __('messages.settings') ?? 'Settings' }}
                            </span>
                        </div>
                        
                        <div class="p-2 space-y-1 bg-dropdown">
                            @can('view_shop_info')
                                <x-sidebar-sub-link href="/settings/shop" :title="__('messages.shop_info') ?? 'Shop Info'" />
                            @endcan
                            
                            @can('view_permissions')
                                <x-sidebar-sub-link href="/settings/permission" :title="__('messages.permission') ?? 'Permission'" />
                            @endcan
                            
                            @can('view_users')
                                <x-sidebar-sub-link href="/settings/users" :title="__('messages.users') ?? 'Users'" />
                            @endcan
                            
                            @can('view_roles')
                                <x-sidebar-sub-link href="/settings/roles" :title="__('messages.roles') ?? 'Roles'" />
                            @endcan
                            
                            @can('view_theme')
                                <x-sidebar-sub-link href="/settings/theme" :title="__('messages.theme') ?? 'Theme Styling'" />
                            @endcan
                        </div>
                    </div>
                </div>

            </div>
        @endcanany

    </nav>
</aside>
</div>