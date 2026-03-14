<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.app_name') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-screen bg-gray-50 flex overflow-hidden font-sans antialiased">

    <x-sidebar />

    <div class="flex-1 flex flex-col h-full overflow-hidden">
        
        <livewire:header />

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
            
            {{ $slot }} @livewireScripts

        </main>
    </div>

</body>
</html>