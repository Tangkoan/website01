<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.app_name') ?? 'Admin Panel' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body x-data="{ sidebarOpen: false }" class="h-screen bg-gray-50 flex overflow-hidden font-sans antialiased">

    <x-sidebar />

    <div class="flex-1 flex flex-col h-full overflow-hidden w-full">
        <livewire:header />

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 md:p-6">
            {{ $slot }} 
        </main>
    </div>

    @livewireScripts
</body>
</html>