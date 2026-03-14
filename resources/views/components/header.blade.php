<header class="h-16 bg-white shadow-sm border-b border-gray-200 flex items-center justify-between px-4 md:px-6 z-10 relative">
    
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = true" class="md:hidden p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg focus:outline-none transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        
        <div class="text-xl font-semibold text-gray-800 hidden sm:block">Admin Panel</div>
    </div>

    <div class="flex items-center gap-3 md:gap-6">
        
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium focus:outline-none transition-colors">
                <span>🌐</span>
                <span class="hidden sm:inline">{{ app()->getLocale() == 'km' ? 'ខ្មែរ' : 'English' }}</span>
                <span class="sm:hidden">{{ app()->getLocale() == 'km' ? 'KM' : 'EN' }}</span>
            </button>
            <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-lg border py-2 z-50">
                <button wire:click="changeLanguage('km')" class="w-full text-left px-4 py-2 text-sm {{ app()->getLocale() == 'km' ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-700 hover:bg-gray-50' }}">ខ្មែរ</button>
                <button wire:click="changeLanguage('en')" class="w-full text-left px-4 py-2 text-sm {{ app()->getLocale() == 'en' ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-700 hover:bg-gray-50' }}">English</button>
            </div>
        </div>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border-2 border-blue-200 hover:ring-2 hover:ring-blue-300 transition-all">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
            </button>
            <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border py-2 z-50">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">👤 {{ __('messages.profile') ?? 'ប្រវត្តិរូប' }}</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">🔑 {{ __('messages.change_password') ?? 'ប្ដូរពាក្យសម្ងាត់' }}</a>
                <hr class="my-1 border-gray-100">
                <button wire:click="logout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold transition-colors">
                    <span wire:loading.remove wire:target="logout">🚪 {{ __('messages.logout_btn') ?? 'ចាកចេញ' }}</span>
                    <span wire:loading wire:target="logout" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        កំពុងចាកចេញ...
                    </span>
                </button>
            </div>
        </div>
        
    </div>
</header>