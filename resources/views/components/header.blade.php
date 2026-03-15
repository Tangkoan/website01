<header class="h-16 bg-header shadow-sm border-b border-border-color flex items-center justify-between px-4 md:px-6 z-10 relative transition-colors duration-200">
    
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = true" class="md:hidden p-2 text-text-muted hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all active:scale-95">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden md:block p-2 text-text-muted hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="!sidebarCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h18" />
                <path x-show="sidebarCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
            </svg>
        </button>
        
        <div class="text-xl font-bold text-text-main hidden sm:block">{{ __('messages.app_name') }}</div>
    </div>

    <div class="flex items-center gap-2 sm:gap-4">
        <button 
            x-data="{ 
                isDark: document.documentElement.classList.contains('dark'),
                toggleTheme() {
                    this.isDark = !this.isDark;
                    document.documentElement.classList.toggle('dark', this.isDark);
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                }
            }" 
            @click="toggleTheme()" 
            class="p-2 text-text-muted hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all duration-300"
        >
            <svg x-show="isDark" x-cloak class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg x-show="!isDark" x-cloak class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 p-2 text-text-main hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all">
                <img src="https://flagcdn.com/w40/{{ app()->getLocale() == 'km' ? 'kh' : 'us' }}.png" class="w-5 h-5 rounded-full object-cover shadow-sm">
                <span class="hidden md:inline text-sm font-semibold">{{ app()->getLocale() == 'km' ? 'ខ្មែរ' : 'English' }}</span>
            </button>
            <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-40 bg-dropdown rounded-xl shadow-xl border border-border-color py-1 z-50 overflow-hidden">
                <button wire:click="changeLanguage('km')" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() == 'km' ? 'text-primary font-bold' : 'text-text-main' }}">
                    <img src="https://flagcdn.com/w40/kh.png" class="w-5 h-5 rounded-full object-cover"> ខ្មែរ
                </button>
                <button wire:click="changeLanguage('en')" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() == 'en' ? 'text-primary font-bold' : 'text-text-main' }}">
                    <img src="https://flagcdn.com/w40/us.png" class="w-5 h-5 rounded-full object-cover"> English
                </button>
            </div>
        </div>

        <div x-data="{ open: false }" class="relative border-l border-border-color pl-2">
            <button @click="open = !open" @click.away="open = false" class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold border border-primary/20 hover:border-primary/40 transition-all">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </button>
            <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-48 bg-dropdown rounded-xl shadow-xl border border-border-color py-2 z-50">
                <a href="#" class="block px-4 py-2 text-sm text-text-main hover:bg-gray-100 dark:hover:bg-gray-700">👤 {{ __('messages.profile') }}</a>
                <hr class="my-1 border-border-color">
                <button wire:click="logout" class="w-full text-left px-4 py-2 text-sm text-red-500 font-bold hover:bg-red-50 dark:hover:bg-red-900/10">
                    🚪 {{ __('messages.logout_btn') }}
                </button>
            </div>
        </div>
    </div>
</header>