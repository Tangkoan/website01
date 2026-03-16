<div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 bg-gray-900/40 dark:bg-black/60 backdrop-blur-sm z-20 md:hidden" x-cloak></div>

<aside 
    :class="{ 'w-[260px]': !sidebarCollapsed, 'w-[80px]': sidebarCollapsed, 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
    class="fixed inset-y-0 left-0 z-30 bg-white dark:bg-[#0f172a] text-text-main border-r border-gray-200/80 dark:border-gray-800 flex flex-col h-full shadow-sm transition-all duration-300 ease-in-out md:relative md:translate-x-0"
>
    <div class="h-16 flex items-center justify-center sm:justify-start px-6 border-b border-gray-200/80 dark:border-gray-800">
        <div class="flex items-center gap-3 min-w-max">
            <span class="text-2xl drop-shadow-sm transition-transform hover:scale-110">⚡</span>
            <h1 x-show="!sidebarCollapsed" x-transition.opacity class="text-lg font-bold tracking-wide text-gray-900 dark:text-white uppercase">
                Nova<span class="text-primary font-black">Dash</span>
            </h1>
        </div>
    </div>

    <nav class="flex-1 py-4 overflow-y-auto no-scrollbar">
        
        <div x-show="!sidebarCollapsed" class="px-6 mb-2 mt-2" x-transition.opacity>
            <p class="text-[11px] font-bold tracking-[0.15em] text-gray-400 dark:text-gray-500 uppercase">Main Menu</p>
        </div>

        <a wire:navigate href="/dashboard" 
           class="group relative flex items-center gap-4 px-6 py-3.5 transition-all duration-200
                  {{ request()->is('dashboard') 
                      ? 'text-primary dark:text-primary bg-gradient-to-r from-primary/15 dark:from-primary/20 to-transparent' 
                      : 'text-text-muted hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-text-main' }}">
            
            @if(request()->is('dashboard'))
                <div class="absolute left-0 top-0 bottom-0 w-[5px] bg-primary rounded-r-md shadow-[2px_0_8px_rgba(0,0,0,0.1)]"></div>
            @endif

            <span class="text-[22px] transition-transform duration-300 group-hover:scale-110 {{ request()->is('dashboard') ? 'drop-shadow-sm scale-110' : 'opacity-70 group-hover:opacity-100' }}">
                🏠
            </span>
            
            <span x-show="!sidebarCollapsed" class="tracking-wide whitespace-nowrap {{ request()->is('dashboard') ? 'font-extrabold' : 'font-semibold' }}">
                {{ __('messages.home') ?? 'Dashboard' }}
            </span>
        </a>

        <div x-show="!sidebarCollapsed" class="px-6 mb-2 mt-6" x-transition.opacity>
            <p class="text-[11px] font-bold tracking-[0.15em] text-gray-400 dark:text-gray-500 uppercase">Management</p>
        </div>

        <div x-data="{ 
                open: {{ request()->is('settings*') ? 'true' : 'false' }}, 
                hovered: false,
                timeout: null 
             }" 
             @mouseenter="hovered = true; clearTimeout(timeout)" 
             @mouseleave="timeout = setTimeout(() => hovered = false, 200)"
             class="relative">
            
            <button @click="sidebarCollapsed ? null : open = !open" 
                class="w-full group relative flex items-center justify-between px-6 py-3.5 transition-all duration-200
                       {{ request()->is('settings*') 
                           ? 'text-primary dark:text-primary bg-gradient-to-r from-primary/15 dark:from-primary/20 to-transparent' 
                           : 'text-text-muted hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-text-main' }}">
                
                @if(request()->is('settings*'))
                    <div class="absolute left-0 top-0 bottom-0 w-[5px] bg-primary rounded-r-md shadow-[2px_0_8px_rgba(0,0,0,0.1)]"></div>
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
                    <svg :class="open ? 'rotate-180 text-primary' : 'text-gray-400 group-hover:text-text-main dark:group-hover:text-gray-300'" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </button>

            <div x-show="open && !sidebarCollapsed" x-cloak x-collapse class="relative bg-primary/5 dark:bg-gray-800/10">
                <div class="absolute left-[34px] top-0 bottom-0 w-px bg-primary/20 dark:bg-gray-700"></div>

                <div class="py-2 space-y-0.5">
                    <div class="pl-14 pr-6 relative">
                        <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-primary/20 dark:bg-gray-700"></div>
                        <x-sidebar-sub-link href="/settings/shop" :title="__('messages.shop_info') ?? 'Shop Info'" />
                    </div>
                    
                    <div class="pl-14 pr-6 relative">
                        <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-primary/20 dark:bg-gray-700"></div>
                        <x-sidebar-sub-link href="/users" :title="__('messages.users') ?? 'Users'" />
                    </div>

                    <div class="pl-14 pr-6 relative">
                        <div class="absolute left-[34px] top-1/2 -translate-y-1/2 w-3 h-px bg-primary/20 dark:bg-gray-700"></div>
                        <x-sidebar-sub-link href="/settings/roles" :title="__('messages.roles') ?? 'Roles'" />
                    </div>
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

                <div class="bg-white dark:bg-[#1e293b] border border-gray-100 dark:border-gray-700 shadow-xl shadow-primary/5 rounded-xl overflow-hidden relative">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-primary"></div>
                    
                    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/80 bg-primary/5 dark:bg-[#1e293b] mt-1">
                        <span class="text-xs font-bold uppercase tracking-widest text-primary dark:text-gray-200">
                            {{ __('messages.settings') ?? 'Settings' }}
                        </span>
                    </div>
                    
                    <div class="p-2 space-y-1 bg-white dark:bg-[#0f172a]/50">
                        <x-sidebar-sub-link href="/settings/shop" :title="__('messages.shop_info') ?? 'Shop Info'" />
                        <x-sidebar-sub-link href="/users" :title="__('messages.users') ?? 'Users'" />
                        <x-sidebar-sub-link href="/settings/roles" :title="__('messages.roles') ?? 'Roles'" />
                    </div>
                </div>
            </div>

        </div>
    </nav>
</aside>