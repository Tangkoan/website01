<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    @php
        // ទាញយក Theme ដែល Active (គួរតែប្រើ Cache ក្នុងគម្រោងពិតប្រាកដ ដើម្បីដើរលឿន)
        $theme = \App\Models\Theme::where('is_active', true)->first();
        $colors = $theme ? $theme->colors : [];
    @endphp

    <script>
        function applyTheme() {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        }

        // រត់នៅពេល Load ទំព័រដំបូង
        applyTheme();

        // រត់រាល់ពេល Livewire ប្តូរទំព័រ (wire:navigate)
        document.addEventListener('livewire:navigated', applyTheme);
    </script>

    <style>
        /* សម្រាប់ Light Mode (ទម្រង់ដើម) */
        :root {
            @if(isset($colors['light']))
                @foreach($colors['light'] as $key => $value)
                    --color-{{ str_replace('_', '-', $key) }}: {{ $value }};
                @endforeach
            @endif
        }

        /* សម្រាប់ Dark Mode (នៅពេលមាន Class "dark" នៅលើ <html>) */
        .dark {
            @if(isset($colors['dark']))
                @foreach($colors['dark'] as $key => $value)
                    --color-{{ str_replace('_', '-', $key) }}: {{ $value }};
                @endforeach
            @endif
        }
    </style>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.app_name') ?? 'Admin Panel' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body 
    x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' }" 
    x-init="$watch('sidebarCollapsed', value => localStorage.setItem('sidebarCollapsed', value))"
    class="h-screen bg-gray-50 flex overflow-hidden font-sans antialiased dark:bg-gray-900"
>

    <x-sidebar />

    <div class="flex-1 flex flex-col h-full overflow-hidden w-full transition-all duration-300">
        <livewire:header />

        <main id="main-content" class="flex-1 overflow-y-auto p-4">
            <div wire:key="main-container-{{ request()->path() }}">
            {{ $slot }}
        </div>
    </main>
    </div>

    @livewireScripts
</body>
</html>