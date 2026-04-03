@php
    // ទាញយក Setting ថ្មីសម្រាប់ Sidebar
    $sidebarMode = Illuminate\Support\Facades\Cache::rememberForever('setting_sidebar_ui_mode', function () {
        $setting = \App\Models\Setting::where('key', 'sidebar_ui_mode')->first();
        return $setting ? $setting->value : 'hide';
    });

    // ឆែកសិទ្ធិសម្រាប់ក្រុម USER MANAGEMENT
    $hasAnyUserMgmtPerm = auth()->user()->canany(['view_users', 'view_roles', 'view_permissions', 'manage_role_ui']);
    $showUserMgmtGroup = $hasAnyUserMgmtPerm || $sidebarMode === 'disable';

    // ឆែកសិទ្ធិសម្រាប់ក្រុម SYSTEM SETTINGS
    $hasAnySystemSettingPerm = auth()->user()->canany(['view_shop_info', 'view_theme']);
    $showSystemSettingGroup = $hasAnySystemSettingPerm || $sidebarMode === 'disable';
@endphp

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
        {{-- GROUP 1: USER MANAGEMENT       --}}
        {{-- ============================== --}}
        @if($showUserMgmtGroup)
            <div x-show="!sidebarCollapsed" class="px-6 mb-2 mt-6" x-transition.opacity>
                <p class="text-[11px] font-bold tracking-[0.15em] text-text-muted uppercase">{{ __('messages.user_management') ?? 'Access Control' }}</p>
            </div>

            <div x-data="{ 
                    open: {{ request()->is('settings/users*') || request()->is('settings/roles*') || request()->is('settings/permission*') || request()->is('settings/role-ui*') ? 'true' : 'false' }}, 
                    hovered: false,
                    timeout: null 
                 }" 
                 @mouseenter="hovered = true; clearTimeout(timeout)" 
                 @mouseleave="timeout = setTimeout(() => hovered = false, 200)"
                 class="relative">
                
                <button @click="sidebarCollapsed ? null : open = !open" 
                    class="w-full group relative flex items-center justify-between px-6 py-3.5 transition-all duration-200
                           {{ request()->is('settings/users*') || request()->is('settings/roles*') || request()->is('settings/permission*') || request()->is('settings/role-ui*')
                               ? 'text-primary bg-primary/10' 
                               : 'text-text-muted hover:bg-primary/5 hover:text-text-main' }}">
                    
                    @if(request()->is('settings/users*') || request()->is('settings/roles*') || request()->is('settings/permission*') || request()->is('settings/role-ui*'))
                        <div class="absolute left-0 top-0 bottom-0 w-[5px] bg-primary rounded-r-md shadow-[2px_0_8px_var(--color-primary)] opacity-50"></div>
                    @endif

                    <div class="flex items-center gap-4">
                        <span class="text-[22px] transition-transform duration-300 group-hover:scale-110 {{ request()->is('settings/users*') || request()->is('settings/roles*') || request()->is('settings/permission*') || request()->is('settings/role-ui*') ? 'drop-shadow-sm' : 'opacity-70 group-hover:opacity-100' }}">
                            👥
                        </span>

                         
                        <span x-show="!sidebarCollapsed" class="tracking-wide whitespace-nowrap {{ request()->is('settings/users*') || request()->is('settings/roles*') || request()->is('settings/permission*') || request()->is('settings/role-ui*') ? 'font-extrabold' : 'font-semibold' }}">
                            {{ __('messages.users_roles') ?? 'Users & Roles' }}
                        </span>
                    </div>
                    
                    <div x-show="!sidebarCollapsed" class="flex items-center justify-center transition-colors">
                        <svg :class="open ? 'rotate-180 text-primary' : 'text-text-muted group-hover:text-text-main'" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                <div x-show="open && !sidebarCollapsed" x-cloak x-collapse class="relative bg-dropdown">
                    <div class="absolute left-[34px] top-0 bottom-0 w-px bg-border-color"></div>

                    <div class="py-2 space-y-0.5">
                        @php $canUsers = auth()->user()->can('view_users'); @endphp
                        @if($canUsers || $sidebarMode === 'disable')
                            <div class="pl-14 pr-6 relative {{ !$canUsers ? '!opacity-40 !grayscale !pointer-events-none' : '' }}">
                                <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-border-color"></div>
                                <x-sidebar-sub-link href="/settings/users" :title="__('messages.users')" />
                            </div>
                        @endif

                        @php $canRoles = auth()->user()->can('view_roles'); @endphp
                        @if($canRoles || $sidebarMode === 'disable')
                            <div class="pl-14 pr-6 relative {{ !$canRoles ? '!opacity-40 !grayscale !pointer-events-none' : '' }}">
                                <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-border-color"></div>
                                <x-sidebar-sub-link href="/settings/roles" :title="__('messages.roles')" />
                            </div>
                        @endif
                        
                        @php $canPerm = auth()->user()->can('view_permissions'); @endphp
                        @if($canPerm || $sidebarMode === 'disable')
                            <div class="pl-14 pr-6 relative {{ !$canPerm ? '!opacity-40 !grayscale !pointer-events-none' : '' }}">
                                <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-border-color"></div>
                                <x-sidebar-sub-link href="/settings/permission" :title="__('messages.permission')" />
                            </div>
                        @endif

                        @php $canRoleUi = auth()->user()->can('manage_role_ui'); @endphp
                        @if($canRoleUi || $sidebarMode === 'disable')
                            <div class="pl-14 pr-6 relative {{ !$canRoleUi ? '!opacity-40 !grayscale !pointer-events-none' : '' }}">
                                <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-border-color"></div>
                                <x-sidebar-sub-link href="/settings/role-ui" :title="__('messages.role_ui_mode') ?? 'Role UI Mode'" />
                            </div>
                        @endif
                    </div>
                </div>

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
                                {{ __('messages.users_roles') ?? 'Users & Roles' }}
                            </span>
                        </div>
                        
                        <div class="p-2 space-y-1 bg-dropdown">
                            @if($canUsers || $sidebarMode === 'disable')
                                <div class="{{ !$canUsers ? '!opacity-40 !grayscale !pointer-events-none' : '' }}">
                                    <x-sidebar-sub-link href="/settings/users" :title="__('messages.users')" />
                                </div>
                            @endif
                            @if($canRoles || $sidebarMode === 'disable')
                                <div class="{{ !$canRoles ? '!opacity-40 !grayscale !pointer-events-none' : '' }}">
                                    <x-sidebar-sub-link href="/settings/roles" :title="__('messages.roles')" />
                                </div>
                            @endif
                            @if($canPerm || $sidebarMode === 'disable')
                                <div class="{{ !$canPerm ? '!opacity-40 !grayscale !pointer-events-none' : '' }}">
                                    <x-sidebar-sub-link href="/settings/permission" :title="__('messages.permission')" />
                                </div>
                            @endif
                            @if($canRoleUi || $sidebarMode === 'disable')
                                <div class="{{ !$canRoleUi ? '!opacity-40 !grayscale !pointer-events-none' : '' }}">
                                    <x-sidebar-sub-link href="/settings/role-ui" :title="__('messages.role_ui_mode') ?? 'Role UI Mode'" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============================== --}}
        {{-- GROUP 2: SYSTEM SETTINGS       --}}
        {{-- ============================== --}}
        @if($showSystemSettingGroup)
            <div x-show="!sidebarCollapsed" class="px-6 mb-2 mt-6" x-transition.opacity>
                <p class="text-[11px] font-bold tracking-[0.15em] text-text-muted uppercase">{{ __('messages.system') ?? 'System' }}</p>
            </div>

            <div x-data="{ 
                    open: {{ request()->is('settings/shop*') || request()->is('settings/theme*') ? 'true' : 'false' }}, 
                    hovered: false,
                    timeout: null 
                 }" 
                 @mouseenter="hovered = true; clearTimeout(timeout)" 
                 @mouseleave="timeout = setTimeout(() => hovered = false, 200)"
                 class="relative">
                
                <button @click="sidebarCollapsed ? null : open = !open" 
                    class="w-full group relative flex items-center justify-between px-6 py-3.5 transition-all duration-200
                           {{ request()->is('settings/shop*') || request()->is('settings/theme*')
                               ? 'text-primary bg-primary/10' 
                               : 'text-text-muted hover:bg-primary/5 hover:text-text-main' }}">
                    
                    @if(request()->is('settings/shop*') || request()->is('settings/theme*'))
                        <div class="absolute left-0 top-0 bottom-0 w-[5px] bg-primary rounded-r-md shadow-[2px_0_8px_var(--color-primary)] opacity-50"></div>
                    @endif

                    <div class="flex items-center gap-4">
                        <span class="text-[22px] transition-transform duration-300 group-hover:rotate-45 {{ request()->is('settings/shop*') || request()->is('settings/theme*') ? 'drop-shadow-sm' : 'opacity-70 group-hover:opacity-100' }}">
                            ⚙️
                        </span>
                        <span x-show="!sidebarCollapsed" class="tracking-wide whitespace-nowrap {{ request()->is('settings/shop*') || request()->is('settings/theme*') ? 'font-extrabold' : 'font-semibold' }}">
                            {{ __('messages.settings') ?? 'Settings' }}
                        </span>
                    </div>
                    
                    <div x-show="!sidebarCollapsed" class="flex items-center justify-center transition-colors">
                        <svg :class="open ? 'rotate-180 text-primary' : 'text-text-muted group-hover:text-text-main'" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                <div x-show="open && !sidebarCollapsed" x-cloak x-collapse class="relative bg-dropdown">
                    <div class="absolute left-[34px] top-0 bottom-0 w-px bg-border-color"></div>

                    <div class="py-2 space-y-0.5">
                        @php $canShop = auth()->user()->can('view_shop_info'); @endphp
                        @if($canShop || $sidebarMode === 'disable')
                            <div class="pl-14 pr-6 relative {{ !$canShop ? '!opacity-40 !grayscale !pointer-events-none' : '' }}">
                                <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-border-color"></div>
                                <x-sidebar-sub-link href="/settings/shop" :title="__('messages.shop_info')" />
                            </div>
                        @endif

                        @php $canTheme = auth()->user()->can('view_theme'); @endphp
                        @if($canTheme || $sidebarMode === 'disable')
                            <div class="pl-14 pr-6 relative {{ !$canTheme ? '!opacity-40 !grayscale !pointer-events-none' : '' }}">
                                <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-border-color"></div>
                                <x-sidebar-sub-link href="/settings/theme" :title="__('messages.theme')" />
                            </div>
                        @endif
                    </div>
                </div>

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
                            @if($canShop || $sidebarMode === 'disable')
                                <div class="{{ !$canShop ? '!opacity-40 !grayscale !pointer-events-none' : '' }}">
                                    <x-sidebar-sub-link href="/settings/shop" :title="__('messages.shop_info')" />
                                </div>
                            @endif
                            @if($canTheme || $sidebarMode === 'disable')
                                <div class="{{ !$canTheme ? '!opacity-40 !grayscale !pointer-events-none' : '' }}">
                                    <x-sidebar-sub-link href="/settings/theme" :title="__('messages.theme')" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </nav>
</aside>
</div>