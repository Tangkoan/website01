<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        // ទាញយក Theme ដែល Active (គួរតែប្រើ Cache ក្នុងគម្រោងពិតប្រាកដ ដើម្បីដើរលឿន)
        $theme = \App\Models\Theme::where('is_active', true)->first();
        $colors = $theme ? $theme->colors : [];
    @endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Battambang:wght@100;300;400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

{{-- Icon --}}
{{-- <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script> --}}
{{-- <script src="{{ asset('assets/js/iconify.min.js') }}"></script>
<script src="{{ asset('assets/js/twemoji.min.js') }}"></script> --}}
<script src="{{ asset('assets/js/iconify-icon.min.js') }}"></script>



<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/nano.min.css"/>
{{-- <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script> --}}

    <script>
        function applyTheme() {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                    '(prefers-color-scheme: dark)').matches)) {
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
            @if (isset($colors['light']))
                @foreach ($colors['light'] as $key => $value)
                    --color-{{ str_replace('_', '-', $key) }}: {{ $value }};
                @endforeach
            @endif
        }

        /* សម្រាប់ Dark Mode (នៅពេលមាន Class "dark" នៅលើ <html>) */
        .dark {
            @if (isset($colors['dark']))
                @foreach ($colors['dark'] as $key => $value)
                    --color-{{ str_replace('_', '-', $key) }}: {{ $value }};
                @endforeach
            @endif
        }
        
        /* Animation សម្រាប់ Progress Bar របស់ Toast */
        @keyframes shrink {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title : __('messages.app_name') ?? 'Admin Panel' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' }" 
      x-init="$watch('sidebarCollapsed', value => localStorage.setItem('sidebarCollapsed', value))"
      class="h-screen bg-gray-50 flex overflow-hidden font-sans antialiased dark:bg-gray-900">

    {{-- <livewire:sidebar /> --}}
    <livewire:settings.sidebar-provider />
    

    <div class="flex-1 flex flex-col h-full overflow-hidden w-full transition-all duration-300">
        <livewire:header />

        <main id="main-content" class="flex-1 overflow-y-auto p-4">
            <div wire:key="main-container-{{ request()->path() }}">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts

    <div
     wire:ignore
     x-data="{
            toasts: [],
            addToast(toast) {
                // ២. បង្កើត ID ដែលមានភាព Unique ខ្លាំងមិនជាន់គ្នា
                const id = Date.now() + Math.random().toString(36).substring(2, 9);
                const duration = toast.duration || 3000; 
                this.toasts.push({ id, duration, ...toast });
            },
            removeToast(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }
        }"
        @notify.window="addToast($event.detail)"
        class="fixed top-20 right-5 z-[9999] flex flex-col gap-3 pointer-events-none"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div x-data="{
                    timer: null,
                    remaining: toast.duration,
                    startTime: 0,
                    isPaused: false,
                    
                    startTimer() {
                        if (this.remaining > 0) {
                            this.isPaused = false;
                            this.startTime = Date.now();
                            this.timer = setTimeout(() => {
                                removeToast(toast.id); // ហៅអនុគមន៍លុបពី Global
                            }, this.remaining);
                        }
                    },
                    pauseTimer() {
                        this.isPaused = true;
                        clearTimeout(this.timer); // បញ្ឈប់ការរាប់ថយក្រោយ
                        this.remaining -= (Date.now() - this.startTime); // ដកពេលវេលាដែលបានដើររួចចេញ
                    }
                 }"
                 x-init="startTimer()"
                 @mouseenter="pauseTimer()"
                 @mouseleave="startTimer()"
                 x-show="true"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="relative flex items-start gap-3 px-4 py-3.5 rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.1)] border text-sm font-bold min-w-[280px] max-w-sm pointer-events-auto overflow-hidden group"
                 :class="{
                    'bg-white dark:bg-gray-800 text-green-600 border-green-200 dark:border-green-500/30 dark:text-green-400': toast.type === 'success',
                    'bg-white dark:bg-gray-800 text-red-600 border-red-200 dark:border-red-500/30 dark:text-red-400': toast.type === 'error',
                    'bg-white dark:bg-gray-800 text-yellow-600 border-yellow-200 dark:border-yellow-500/30 dark:text-yellow-400': toast.type === 'warning',
                    'bg-white dark:bg-gray-800 text-blue-600 border-blue-200 dark:border-blue-500/30 dark:text-blue-400': toast.type === 'info'
                 }">
                 
                <div class="mt-0.5 flex-shrink-0">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5 bg-green-100 dark:bg-green-500/20 rounded-full p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-5 h-5 bg-red-100 dark:bg-red-500/20 rounded-full p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-5 h-5 bg-yellow-100 dark:bg-yellow-500/20 rounded-full p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-5 h-5 bg-blue-100 dark:bg-blue-500/20 rounded-full p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </template>
                </div>

                <div class="flex-1 pr-2 text-gray-700 dark:text-gray-200">
                    <span x-text="toast.message" class="block"></span>
                </div>

                <button @click="removeToast(toast.id)" class="flex-shrink-0 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition-colors mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <div class="absolute bottom-0 left-0 h-1 opacity-20"
                     :class="{
                        'bg-green-600': toast.type === 'success',
                        'bg-red-600': toast.type === 'error',
                        'bg-yellow-600': toast.type === 'warning',
                        'bg-blue-600': toast.type === 'info'
                     }"
                     :style="`animation: shrink ${toast.duration}ms linear forwards; animation-play-state: ${isPaused ? 'paused' : 'running'};`">
                </div>
            </div>
        </template>
    </div>
</body>
</html>