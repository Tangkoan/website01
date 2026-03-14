@component('layouts.app')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <div class="relative min-h-[100dvh] flex flex-col items-center justify-center px-4 sm:px-6 py-8 bg-gradient-to-b from-white to-gray-50/50 sm:bg-transparent">
        
        <!-- Background Pattern (Optional) -->
        <div class="absolute inset-0 -z-10 opacity-30 sm:opacity-40">
            <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px]"></div>
        </div>

        <!-- Main Content -->
        <div class="w-full max-w-lg mx-auto text-center">
            
            <!-- 404 Circle with Animation -->
            <div class="relative inline-flex items-center justify-center mb-6 sm:mb-8">
                <div class="absolute inset-0 rounded-full bg-blue-500/5 animate-pulse"></div>
                <div class="relative flex items-center justify-center w-36 h-36 sm:w-44 sm:h-44 bg-gradient-to-br from-blue-50 to-indigo-50/50 rounded-full shadow-inner">
                    <h1 class="text-[5rem] sm:text-7xl font-black bg-gradient-to-br from-blue-600 to-indigo-600 bg-clip-text text-transparent tracking-tighter drop-shadow-sm">404</h1>
                </div>
            </div>
            
            <!-- Title -->
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                {{ __('messages.404_title') }}
            </h2>
            
            <!-- Description -->
            <p class="text-sm sm:text-base text-gray-500 max-w-sm mx-auto mb-8 sm:mb-10 leading-relaxed">
                {{ __('messages.404_description') }}
            </p>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 w-full">
                
                <!-- Back Button -->
                <a href="{{ url()->previous() }}" 
                   wire:navigate 
                   class="group flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 sm:py-3.5 bg-white text-gray-700 text-sm sm:text-base font-medium rounded-xl border border-gray-200 shadow-sm hover:bg-gray-50 hover:border-gray-300 active:bg-gray-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 group-hover:text-gray-600 transition-colors" 
                         fill="none" 
                         viewBox="0 0 24 24" 
                         stroke-width="2" 
                         stroke="currentColor">
                        <path stroke-linecap="round" 
                              stroke-linejoin="round" 
                              d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    
                    <span>{{ __('messages.go_back') }}</span>
                </a>

                <!-- Home Button -->
                <a href="/dashboard" 
                   wire:navigate 
                   class="group flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 sm:py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm sm:text-base font-medium rounded-xl shadow-lg shadow-blue-600/25 hover:shadow-xl hover:shadow-blue-600/30 hover:from-blue-700 hover:to-indigo-700 active:scale-[0.98] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                    
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" 
                         fill="none" 
                         viewBox="0 0 24 24" 
                         stroke-width="2" 
                         stroke="currentColor">
                        <path stroke-linecap="round" 
                              stroke-linejoin="round" 
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    
                    <span>{{ __('messages.go_home') }}</span>
                </a>
                
            </div>
            
            <!-- Optional: Additional Help Text -->
            <p class="text-xs text-gray-400 mt-6">
                {{ __('messages.404_help') ?? 'Or check the URL and try again' }}
            </p>
            
        </div>
    </div>
@endcomponent