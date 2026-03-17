<header x-data="{ 
            showLogoutModal: false,
            isFullscreen: false,
            
            // អនុគមន៍សម្រាប់គ្រប់គ្រង Full-Screen
            toggleFullscreen() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.log(`Error attempting to enable fullscreen: ${err.message}`);
                    });
                    this.isFullscreen = true;
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                        this.isFullscreen = false;
                    }
                }
            },
            
            // ស្តាប់ព្រឹត្តិការណ៍ពេល Browser ចេញពី Fullscreen ដោយខ្លួនឯង (ឧទាហរណ៍ចុច Esc)
            init() {
                document.addEventListener('fullscreenchange', () => {
                    this.isFullscreen = !!document.fullscreenElement;
                });
            }
        }" 
        class="h-16 bg-white dark:bg-[#0f172a] shadow-sm dark:shadow-[0_1px_2px_rgba(0,0,0,0.5)] border-b border-gray-200/80 dark:border-gray-800 flex items-center justify-between px-4 md:px-6 z-10 relative transition-colors duration-300">
        
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = true" class="md:hidden p-2 text-gray-500 hover:text-primary hover:bg-primary/5 dark:text-gray-400 dark:hover:bg-gray-800 rounded-xl transition-all active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden md:block p-2 text-gray-400 hover:text-primary hover:bg-primary/5 dark:text-gray-500 dark:hover:text-primary dark:hover:bg-gray-800/80 rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-primary/20 group">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!sidebarCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h18" />
                    <path x-show="sidebarCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                </svg>
            </button>
            
            <div class="text-lg font-bold text-gray-800 dark:text-gray-200 hidden sm:block ml-2 tracking-wide">{{ __('messages.app_name') }}</div>
        </div>

        <div class="flex items-center gap-3 sm:gap-5">
            
            <button @click="toggleFullscreen()" 
                    class="hidden sm:flex items-center justify-center p-1.5 text-gray-400 hover:text-primary hover:bg-primary/5 dark:text-gray-500 dark:hover:text-primary dark:hover:bg-gray-800/80 rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-primary/20 group"
                    :title="isFullscreen ? 'Exit Fullscreen' : 'Enter Fullscreen'">
                
                <svg x-show="!isFullscreen" class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>
                </svg>

                <svg x-show="isFullscreen" x-cloak class="w-5 h-5 transition-transform group-hover:scale-110 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 14h6v6M20 10h-6V4M10 14l-7 7M14 10l7-7"/>
                </svg>
            </button>

            <button 
                x-data="{ 
                    isDark: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
                    
                    init() {
                        this.applyTheme();
                        document.addEventListener('livewire:navigated', () => {
                            this.isDark = localStorage.getItem('theme') === 'dark';
                            this.applyTheme();
                        });
                    },
                    
                    toggleTheme() {
                        this.isDark = !this.isDark;
                        localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                        this.applyTheme();
                    },

                    applyTheme() {
                        if (this.isDark) {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    }
                }" 
                @click="toggleTheme()" 
                class="relative flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary/30 group overflow-hidden bg-gray-100 dark:bg-gray-800 hover:bg-amber-50 dark:hover:bg-indigo-900/30 border border-gray-200 dark:border-gray-700"
                aria-label="Toggle Dark Mode"
                title="Toggle Theme"
            >
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gradient-to-tr"
                    :class="isDark ? 'from-indigo-500/10 to-purple-500/10' : 'from-amber-400/10 to-orange-400/10'"></div>

                <div class="relative w-full h-full flex items-center justify-center transform transition-transform duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
                    :class="isDark ? 'rotate-[360deg]' : 'rotate-0'">
                    
                    <svg class="absolute w-5 h-5 transition-all duration-300 ease-in-out text-amber-500"
                        :class="isDark ? 'opacity-0 scale-50 -rotate-90' : 'opacity-100 scale-100 rotate-0'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2"></path>
                        <path d="M12 20v2"></path>
                        <path d="m4.93 4.93 1.41 1.41"></path>
                        <path d="m17.66 17.66 1.41 1.41"></path>
                        <path d="M2 12h2"></path>
                        <path d="M20 12h2"></path>
                        <path d="m6.34 17.66-1.41 1.41"></path>
                        <path d="m19.07 4.93-1.41 1.41"></path>
                    </svg>

                    <svg x-cloak class="absolute w-5 h-5 transition-all duration-300 ease-in-out text-indigo-400"
                        :class="isDark ? 'opacity-100 scale-100 rotate-0' : 'opacity-0 scale-50 rotate-90'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                        <path d="M19 3v4"></path>
                        <path d="M21 5h-4"></path>
                    </svg>
                </div>
            </button>

            <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 p-1.5 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 rounded-full transition-all border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                    <img src="https://flagcdn.com/w40/{{ app()->getLocale() == 'km' ? 'kh' : 'us' }}.png" class="w-6 h-6 sm:w-7 sm:h-7 rounded-full object-cover shadow-sm ring-2 ring-white dark:ring-gray-800">
                    <span class="hidden md:inline text-sm font-bold mr-1">{{ app()->getLocale() == 'km' ? 'KH' : 'EN' }}</span>
                    <svg class="w-3 h-3 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-36 bg-white dark:bg-[#1e293b] rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-black/50 border border-gray-100 dark:border-gray-700 py-2 z-50 overflow-hidden">
                    <button type="button" wire:click="changeLanguage('km')" class="w-full flex items-center gap-3 px-4 py-2 text-sm hover:bg-primary/5 dark:hover:bg-gray-700/50 transition-colors {{ app()->getLocale() == 'km' ? 'text-primary font-bold' : 'text-gray-600 dark:text-gray-300 font-medium' }}">
                        <img src="https://flagcdn.com/w40/kh.png" class="w-5 h-5 rounded-full object-cover shadow-sm"> ខ្មែរ
                        @if(app()->getLocale() == 'km') <svg class="w-4 h-4 ml-auto text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> @endif
                    </button>
                    <button type="button" wire:click="changeLanguage('en')" class="w-full flex items-center gap-3 px-4 py-2 text-sm hover:bg-primary/5 dark:hover:bg-gray-700/50 transition-colors {{ app()->getLocale() == 'en' ? 'text-primary font-bold' : 'text-gray-600 dark:text-gray-300 font-medium' }}">
                        <img src="https://flagcdn.com/w40/us.png" class="w-5 h-5 rounded-full object-cover shadow-sm"> English
                        @if(app()->getLocale() == 'en') <svg class="w-4 h-4 ml-auto text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> @endif
                    </button>
                </div>
            </div>

            <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

            <div x-data="{ open: false }" @click.outside="open = false" class="relative pl-1">
            <button @click="open = !open" 
                    class="flex items-center gap-2 p-1 pl-1.5 pr-3 rounded-full transition-all duration-300 outline-none focus:ring-2 focus:ring-primary/40 group bg-gray-50/50 hover:bg-gray-100 dark:bg-gray-800/50 dark:hover:bg-gray-800 border border-transparent hover:border-gray-200 dark:hover:border-gray-700"
                    :class="open ? 'bg-gray-100 dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow-sm' : ''">
                
                <div class="relative w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-tr from-primary to-indigo-500 p-[2px] shadow-sm group-hover:shadow-md transition-shadow">
                    <div class="w-full h-full rounded-full bg-white dark:bg-[#0f172a] flex items-center justify-center overflow-hidden">
                        
                        @if (auth()->user()->image)
                            <img src="{{ Storage::url(auth()->user()->image) }}" alt="Profile Photo" class="w-full h-full object-cover">
                        @else
                            <span class="text-primary font-black text-xs sm:text-sm">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                        @endif

                    </div>
                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white dark:bg-[#0f172a] rounded-full shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                </div>
                
                <div class="hidden sm:block text-left ml-1">
                    <p class="text-[13px] font-bold text-gray-800 dark:text-gray-200 leading-tight">
                        {{ Str::limit(auth()->user()->name ?? 'User', 10) }}
                    </p>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider">
                        {{ auth()->user()->roles->pluck('name')->first() ?? 'User' }}
                    </p>
                </div>
                
                <svg :class="open ? 'rotate-180 text-primary' : 'text-gray-400'" class="hidden sm:block w-4 h-4 ml-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
            </button>

            <div x-show="open" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="absolute right-0 mt-3 w-64 bg-white/95 dark:bg-[#1e293b]/95 backdrop-blur-xl rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] dark:shadow-[0_10px_40px_-10px_rgba(0,0,0,0.5)] border border-gray-100 dark:border-gray-700/80 py-2 z-50 overflow-hidden origin-top-right">
                
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/80 mb-1 bg-gray-50/50 dark:bg-gray-900/30">
                    <p class="text-[15px] font-bold text-gray-900 dark:text-white truncate">{{ auth()->user()->name ?? 'User Account' }}</p>
                    <p class="text-[13px] text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ auth()->user()->email ?? 'user@example.com' }}</p>
                </div>
                
                <div class="px-2">
                    <a href="{{ route('profile.edit') }}" wire:navigate class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14px] text-gray-700 dark:text-gray-300 font-medium hover:bg-primary/5 hover:text-primary dark:hover:bg-primary/10 transition-colors">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 group-hover:bg-white dark:group-hover:bg-gray-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4 text-gray-500 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        {{ __('messages.profile') ?? 'My Profile' }}
                    </a>
                    
                    <a href="{{ route('password.change') }}" wire:navigate class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14px] text-gray-700 dark:text-gray-300 font-medium hover:bg-primary/5 hover:text-primary dark:hover:bg-primary/10 transition-colors">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 group-hover:bg-white dark:group-hover:bg-gray-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4 text-gray-500 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        {{ __('messages.change_password') ?? 'Change Password' }}
                    </a>
                </div>
                
                <div class="my-1.5 border-t border-gray-100 dark:border-gray-700/80 mx-3"></div>
                
                <div class="px-2">
                    <button type="button" @click="showLogoutModal = true; open = false" class="w-full group flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14px] text-red-600 dark:text-red-400 font-bold hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/30 group-hover:bg-red-100 dark:group-hover:bg-red-900/50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                        </div>
                        {{ __('messages.logout_btn') ?? 'Sign out' }}
                    </button>
                </div>
            </div>
        </div>
        </div>

        <template x-teleport="body">
            <div x-show="showLogoutModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
                <div x-show="showLogoutModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/40 dark:bg-black/60 backdrop-blur-sm" @click="showLogoutModal = false"></div>

                <div x-show="showLogoutModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative bg-white dark:bg-[#1e293b] rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden z-[101] transform transition-all border border-gray-100 dark:border-gray-700">
                    <div class="p-6">
                        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100 dark:bg-red-900/30 mb-5">
                            <svg class="h-7 w-7 text-red-600 dark:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2" id="modal-title">{{ __('messages.confirm_logout_title') ?? 'Ready to Leave?' }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.confirm_logout_msg') ?? 'Are you sure you want to log out of your account?' }}</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-[#0f172a]/50 px-6 py-4 flex flex-row gap-3 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" @click="showLogoutModal = false" class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                        <button type="button" wire:click="logout" @click="showLogoutModal = false" class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-red-600 border border-transparent rounded-xl text-sm font-semibold text-white hover:bg-red-700 shadow-sm shadow-red-600/20 transition-colors">{{ __('messages.confirm') ?? 'Logout' }}</button>
                    </div>
                </div>
            </div>
        </template>
    </header>