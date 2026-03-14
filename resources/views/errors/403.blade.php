{{-- @component('layouts.app') --}}
    <div class="flex flex-col items-center justify-center min-h-[80vh] text-center px-4">
        
        <div class="relative group cursor-default">
            <div class="absolute inset-0 bg-red-500 opacity-20 blur-3xl rounded-full -z-10 animate-pulse transition-all duration-1000 group-hover:opacity-40 group-hover:blur-2xl"></div>
            <h1 class="text-9xl font-black text-red-200 drop-shadow-xl transition-transform duration-500 hover:scale-110">403</h1>
        </div>

        <h2 class="text-3xl font-bold text-gray-800 mt-6 tracking-tight">{{ __('messages.403_title') }}</h2>
        <p class="text-gray-500 mt-2 mb-10 max-w-md">{{ __('messages.403_description') }}</p>
        
        <div class="flex gap-4 justify-center">
            <a href="{{ url()->previous() }}" wire:navigate class="group flex items-center gap-2 px-6 py-3 bg-gray-800 text-white rounded-xl shadow-md hover:bg-gray-900 hover:shadow-xl hover:-translate-y-1 active:scale-95 transition-all duration-300">
                <span class="group-hover:-translate-x-1 transition-transform duration-300">⬅️</span>
                <span class="font-medium">{{ __('messages.go_back') }}</span>
            </a>
            
            <a href="/dashboard" wire:navigate class="group flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl shadow-md shadow-blue-600/20 hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-600/40 hover:-translate-y-1 active:scale-95 transition-all duration-300">
                <span class="group-hover:scale-125 transition-transform duration-300">🏠</span>
                <span class="font-medium">{{ __('messages.go_home') }}</span>
            </a>
        </div>
    </div>
{{-- @endcomponent --}}