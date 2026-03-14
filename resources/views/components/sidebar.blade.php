<aside class="w-64 bg-gray-900 text-white flex flex-col h-full shadow-lg transition-all duration-300">
    <div class="h-16 flex items-center justify-center border-b border-gray-800">
        <h1 class="text-2xl font-bold tracking-wider text-blue-400">My App</h1>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <a wire:navigate href="/dashboard" class="flex items-center gap-3 px-4 py-3 bg-gray-800 text-white rounded-xl transition-colors">
            <span>🏠</span> 
            <span class="font-medium">Dashboard</span>
        </a>

        <a wire:navigate href="/users" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition-colors">
            <span>👥</span> 
            <span class="font-medium">អ្នកប្រើប្រាស់</span>
        </a>

        <a wire:navigate href="/settings" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition-colors">
            <span>⚙️</span> 
            <span class="font-medium">ការកំណត់</span>
        </a>
    </nav>
</aside>