{{-- @component('layouts.app') --}}
    <div class="flex flex-col items-center justify-center min-h-[80vh] text-center px-4">
        <h1 class="text-9xl font-black text-gray-200">404</h1>
        <h2 class="text-3xl font-bold text-gray-800 mt-4">{{ __('messages.404_title') }}</h2>
        <p class="text-gray-500 mt-2 mb-8">{{ __('messages.404_description') }}</p>
        
        <div class="flex gap-4 justify-center">
            <a href="{{ url()->previous() }}" wire:navigate class="flex items-center gap-2 px-6 py-3 bg-gray-800 text-white rounded-xl hover:bg-gray-900 transition-colors">
                <span>⬅️</span>
                <span class="font-medium">{{ __('messages.go_back') }}</span>
            </a>
            
            <a href="/dashboard" wire:navigate class="flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                <span>🏠</span>
                <span class="font-medium">{{ __('messages.go_home') }}</span>
            </a>
        </div>
    </div>
{{-- @endcomponent --}}