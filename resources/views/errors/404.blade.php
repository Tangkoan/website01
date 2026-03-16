@component('layouts.app')
    <div class="relative min-h-[calc(100vh-80px)] w-full flex flex-col justify-between bg-gradient-to-b from-white to-gray-50/80 overflow-hidden font-sans">
        
        <div class="absolute inset-0 -z-10 opacity-40">
            <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px]"></div>
        </div>

        <div class="pt-8"></div>

        <div class="flex flex-col items-center justify-center px-6">
            
            <div class="relative inline-flex items-center justify-center mb-6">
                <div class="absolute inset-0 rounded-full bg-blue-500/10 animate-pulse"></div>
                <div class="relative flex items-center justify-center w-36 h-36 bg-gradient-to-br from-blue-50 to-indigo-50/40 rounded-full shadow-[inset_0_2px_10px_rgba(0,0,0,0.02)] border border-white">
                    <h1 class="text-6xl font-black bg-gradient-to-br from-blue-600 to-indigo-600 bg-clip-text text-transparent tracking-tighter">404</h1>
                </div>
            </div>
            
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3 text-center">
                {{ __('messages.404_title') }}
            </h2>
            
            <p class="text-sm text-gray-500 text-center max-w-[280px] leading-relaxed">
                {{ __('messages.404_description') }}
            </p>
            
        </div>

        <div class="w-full max-w-lg mx-auto px-5 pb-8 sm:pb-12 pt-10 mt-auto">
            
            <div class="flex flex-row gap-3 w-full">
                <a href="{{ url()->previous() }}" 
                   wire:navigate 
                   class="flex-1 flex items-center justify-center gap-2 px-2 py-4 bg-white text-gray-700 text-[15px] font-semibold rounded-2xl border border-gray-200/80 shadow-sm active:scale-[0.97] active:bg-gray-50 transition-all duration-200">
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>{{ __('messages.go_back') }}</span>
                </a>

                <a href="/dashboard" 
                   wire:navigate 
                   class="flex-1 flex items-center justify-center gap-2 px-2 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-[15px] font-semibold rounded-2xl shadow-md shadow-blue-600/20 active:scale-[0.97] transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>{{ __('messages.go_home') }}</span>
                </a>
            </div>
            
            <p class="text-[11px] text-gray-400 mt-5 text-center tracking-wide uppercase">
                {{ __('messages.404_help') ?? 'Check the URL and try again' }}
            </p>
            
        </div>
        
    </div>
@endcomponent