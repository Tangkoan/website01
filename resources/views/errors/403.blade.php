<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 Access Denied - {{ __('messages.app_name') ?? config('app.name') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- បន្ថែម Livewire Styles នៅទីនេះ --}}
    @livewireStyles
    
    <script>
        // កូដសម្រាប់ឆែក Dark Mode ឱ្យស្គាល់ស្វ័យប្រវត្តិ
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="antialiased bg-white dark:bg-gray-900">

    <div class="relative min-h-screen w-full flex flex-col justify-between bg-gradient-to-b from-white to-gray-50/80 dark:from-gray-900 dark:to-gray-800 overflow-hidden font-sans transition-colors duration-300">
        
        {{-- Background Pattern --}}
        <div class="absolute inset-0 -z-10 opacity-40 dark:opacity-20">
            <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] dark:bg-[radial-gradient(#374151_1px,transparent_1px)] [background-size:16px_16px]"></div>
        </div>

        <div class="pt-12"></div>

        <div class="flex flex-col items-center justify-center px-6">
            
            {{-- Error Number Badge --}}
            <div class="relative inline-flex items-center justify-center mb-8">
                {{-- Pulse Effect --}}
                <div class="absolute inset-0 rounded-full bg-red-500/10 dark:bg-red-500/5 animate-pulse"></div>
                
                <div class="relative flex items-center justify-center w-40 h-40 bg-gradient-to-br from-red-50 to-rose-50/40 dark:from-gray-800 dark:to-gray-800/50 rounded-full shadow-[inset_0_2px_10px_rgba(0,0,0,0.02)] border border-white dark:border-gray-700">
                    <h1 class="text-6xl font-black bg-gradient-to-br from-red-600 to-rose-600 dark:from-red-400 dark:to-rose-400 bg-clip-text text-transparent tracking-tighter">403</h1>
                </div>

                {{-- Lock Icon Badge --}}
                <div class="absolute -bottom-2 -right-2 bg-white dark:bg-gray-800 p-2.5 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
                    <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
            
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-4 text-center transition-colors duration-300">
                {{ __('messages.403_title') }}
            </h2>
            
            <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 text-center max-w-[320px] leading-relaxed transition-colors duration-300">
                {{ __('messages.403_description') }}
            </p>
            
        </div>

        {{-- Action Buttons --}}
        <div class="w-full max-w-lg mx-auto px-5 pb-12 pt-10 mt-auto">
            
            <div class="flex flex-row gap-3 w-full">
                <a href="{{ url()->previous() }}" 
                   wire:navigate 
                   class="flex-1 flex items-center justify-center gap-2 px-2 py-4 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-[15px] font-semibold rounded-2xl border border-gray-200/80 dark:border-gray-700 shadow-sm active:scale-[0.97] hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>{{ __('messages.go_back') }}</span>
                </a>

                <a href="/dashboard" 
                   wire:navigate 
                   class="flex-1 flex items-center justify-center gap-2 px-2 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-[15px] font-semibold rounded-2xl shadow-md shadow-blue-600/20 dark:shadow-none active:scale-[0.97] hover:from-blue-700 hover:to-indigo-700 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>{{ __('messages.go_home') }}</span>
                </a>
            </div>
            
            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-6 text-center tracking-wide uppercase transition-colors duration-300">
                {{ __('messages.403_help') ?? 'Contact Administrator if you think this is a mistake' }}
            </p>
            
        </div>
        
    </div>

    {{-- បន្ថែម Livewire Scripts នៅទីនេះដើម្បីឱ្យ wire:navigate ដំណើរការ --}}
    @livewireScripts
</body>
</html>