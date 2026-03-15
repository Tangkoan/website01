<div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-20 md:hidden" x-cloak></div>

<aside 
    :class="{ 'w-64': !sidebarCollapsed, 'w-20': sidebarCollapsed, 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
    class="fixed inset-y-0 left-0 z-30 bg-sidebar text-text-main border-r border-border-color flex flex-col h-full shadow-lg transition-all duration-300 ease-in-out md:relative md:translate-x-0"
>
    <div class="h-16 flex items-center px-6 border-b border-border-color">
        <div class="flex items-center gap-3 min-w-max">
            <span class="text-2xl">🚀</span>
            <h1 x-show="!sidebarCollapsed" x-transition.opacity class="text-xl font-black tracking-wider text-primary">My App</h1>
        </div>
    </div>

    <nav class="flex-1 px-3 py-6 space-y-2 overflow-y-auto no-scrollbar">
        <a wire:navigate href="/dashboard" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('dashboard') ? 'bg-primary text-white shadow-md' : 'text-text-muted hover:bg-primary/5 hover:text-primary' }}">
            <span class="text-xl">🏠</span>
            <span x-show="!sidebarCollapsed" class="font-medium whitespace-nowrap">{{ __('messages.home') }}</span>
        </a>

        <div x-data="{ 
                open: {{ request()->is('settings*') ? 'true' : 'false' }}, 
                hovered: false,
                timeout: null 
             }" 
             @mouseenter="hovered = true; clearTimeout(timeout)" 
             @mouseleave="timeout = setTimeout(() => hovered = false, 200)"
             class="relative">
            
            <button @click="sidebarCollapsed ? null : open = !open" 
                class="w-full flex items-center justify-between px-4 py-3 text-text-muted hover:bg-primary/5 hover:text-primary rounded-xl transition-all">
                <div class="flex items-center gap-3">
                    <span class="text-xl">⚙️</span>
                    <span x-show="!sidebarCollapsed" class="font-medium whitespace-nowrap">{{ __('messages.settings') }}</span>
                </div>
                <svg x-show="!sidebarCollapsed" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open && !sidebarCollapsed" x-cloak x-collapse class="pl-11 space-y-1">
                <x-sidebar-sub-link href="/settings/shop" :title="__('messages.shop_info')" />
                <x-sidebar-sub-link href="/users" :title="__('messages.users')" />
                <x-sidebar-sub-link href="/settings/roles" :title="__('messages.roles')" />
            </div>

            <div x-show="hovered && sidebarCollapsed" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 @mouseenter="hovered = true; clearTimeout(timeout)"
                 @mouseleave="hovered = false"
                 class="fixed left-[72px] top-auto ml-1 w-56 z-50 pointer-events-auto"
                 x-cloak>
                 
                <div class="absolute -left-4 top-0 w-4 h-full"></div>

                <div class="bg-sidebar border border-border-color shadow-2xl rounded-2xl overflow-hidden">
                    <div class="px-5 py-3 bg-primary/10 border-b border-border-color">
                        <span class="text-xs font-bold uppercase tracking-widest text-primary">{{ __('messages.settings') }}</span>
                    </div>
                    <div class="p-2 space-y-1">
                        <x-sidebar-sub-link href="/settings/shop" :title="__('messages.shop_info')" />
                        <x-sidebar-sub-link href="/users" :title="__('messages.users')" />
                        <x-sidebar-sub-link href="/settings/roles" :title="__('messages.roles')" />
                        <x-sidebar-sub-link href="/settings/permissions" :title="__('messages.permissions')" />
                    </div>
                </div>
            </div>
        </div>
    </nav>
</aside>