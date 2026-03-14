<header class="h-16 bg-white shadow-sm border-b border-gray-200 flex items-center justify-between px-6 z-10 relative">
    <div class="text-xl font-semibold text-gray-800">Admin Panel</div>

    <div class="flex items-center gap-6">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 text-gray-600 hover:text-blue-600 font-medium focus:outline-none">
                <span>🌐</span>
                <span>{{ app()->getLocale() == 'km' ? 'ខ្មែរ' : 'English' }}</span>
            </button>
            <div x-show="open" x-transition x-cloak class="absolute right-0 mt-4 w-32 bg-white rounded-xl shadow-lg border py-2 z-50">
                <button wire:click="changeLanguage('km')" class="w-full text-left px-4 py-2 text-sm {{ app()->getLocale() == 'km' ? 'bg-blue-50 text-blue-600 font-bold' : '' }}">ខ្មែរ</button>
                <button wire:click="changeLanguage('en')" class="w-full text-left px-4 py-2 text-sm {{ app()->getLocale() == 'en' ? 'bg-blue-50 text-blue-600 font-bold' : '' }}">English</button>
            </div>
        </div>


        

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border-2 border-blue-200">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
            </button>
            <div x-show="open" x-transition x-cloak class="absolute right-0 mt-4 w-48 bg-white rounded-xl shadow-lg border py-2 z-50">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">👤 {{ __('messages.profile') }}</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">🔑 {{ __('messages.change_password') }}</a>
                <hr class="my-1">
                <button wire:click="logout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold">
                    <span wire:loading.remove wire:target="logout">🚪 {{ __('messages.logout_btn') }}</span>
                    <span wire:loading wire:target="logout" wire:click="logout">{{ __('messages.logging_out') }}</span>
                </button>
            </div>
        </div>
    </div>
</header>