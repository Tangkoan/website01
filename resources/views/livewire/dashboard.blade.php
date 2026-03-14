<div class="w-full max-w-2xl mx-auto mt-10 sm:mt-20 bg-white p-6 sm:p-10 rounded-3xl shadow-xl border border-gray-100 sm:mx-auto mx-4 text-center">
    
    <div class="flex justify-end mb-6 space-x-3">
        <button 
            wire:click="changeLanguage('km')" 
            class="text-sm font-medium {{ app()->getLocale() == 'km' ? 'text-blue-600 underline' : 'text-gray-500 hover:text-blue-500' }}"
        >
            ខ្មែរ
        </button>
        
        <span class="text-gray-300">|</span>
        
        <button 
            wire:click="changeLanguage('en')" 
            class="text-sm font-medium {{ app()->getLocale() == 'en' ? 'text-blue-600 underline' : 'text-gray-500 hover:text-blue-500' }}"
        >
            English
        </button>
    </div>

    <h1 class="text-3xl font-extrabold text-gray-900 mb-2">
        {{ __('messages.dashboard_welcome') }}
    </h1>
    
    <h2 class="text-2xl font-bold text-green-600 mb-6 flex justify-center items-center gap-2">
        {{ __('messages.hello') }}, {{ auth()->user()->name ?? __('messages.guest_user') }} <span class="animate-bounce">👋</span>
    </h2>

    <p class="text-gray-500 text-lg mb-10 leading-relaxed">
        {{ __('messages.dashboard_desc') }}
    </p>

    <button 
        wire:click="logout" 
        wire:loading.attr="disabled" 
        class="inline-flex justify-center items-center bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-8 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg focus:ring-4 focus:ring-red-200 disabled:opacity-70 disabled:cursor-not-allowed"
    >
        <span wire:loading.remove>{{ __('messages.logout_btn') }}</span>
        
        <span wire:loading class="flex items-center gap-2">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ __('messages.logging_out') }}
        </span>
    </button>

</div>