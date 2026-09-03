<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Life Reader With Us' }}</title>

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Google AdSense Auto Ads Script -->
    {{-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXXXXXXXXX" crossorigin="anonymous"></script> --}}
</head>
<body class="bg-gray-50 font-sans leading-normal tracking-normal text-gray-800 flex flex-col min-h-screen">

    <!-- Header Component -->
    <x-layouts.header />

    <!-- Top Ad Banner -->
    <div class="w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <x-ads.banner />
    </div>

    <!-- Main Content -->
    <main class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 mb-12">
        {{ $slot }}
    </main>

    <!-- Footer Component -->
    <x-layouts.footer />

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>