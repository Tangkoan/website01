<div 
    x-show="sidebarOpen" 
    x-transition.opacity 
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-black/50 z-20 md:hidden"
></div>

<aside 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 text-white flex flex-col h-full shadow-lg transition-transform duration-300 ease-in-out md:relative md:translate-x-0"
>
    <div class="h-16 flex items-center justify-between px-6 border-b border-gray-800">
        <h1 class="text-2xl font-bold tracking-wider text-blue-400">My App</h1>
        
        <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
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