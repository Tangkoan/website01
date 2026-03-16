<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" 
     x-data="{ 
        activeTab: 'light',
        
        /* ទាញយកទិន្នន័យផ្ទាល់ពី Livewire មក Alpine (Live Preview Magic) */
        light: @entangle('lightColors').live,
        dark: @entangle('darkColors').live,

        /* អនុគមន៍សម្រាប់បាញ់ពណ៌ទៅកាន់ CSS Variables ភ្លាមៗ */
        applyLivePreview() {
            let isDark = document.documentElement.classList.contains('dark');
            let currentColors = isDark ? this.dark : this.light;

            for (const [key, value] of Object.entries(currentColors)) {
                let cssKey = '--color-' + key.replace(/_/g, '-');
                document.documentElement.style.setProperty(cssKey, value);
            }
        }
     }"
     x-init="
        /* ស្តាប់ការផ្លាស់ប្តូរពណ៌ ហើយ Update ភ្លាមៗ */
        $watch('light', value => applyLivePreview());
        $watch('dark', value => applyLivePreview());
        
        /* ស្តាប់ពេលប្តូរ Dark/Light Mode ឲ្យវា Update ពណ៌តាម Theme ភ្លាមៗដែរ */
        const observer = new MutationObserver(() => applyLivePreview());
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        applyLivePreview(); // Run ពេល Load លើកដំបូង
     "
>

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-text-main tracking-tight flex items-center gap-2">
                <span class="text-3xl">🎨</span>
                {{ __('messages.theme_customization') ?? 'Theme Customization' }}
            </h2>
           
        </div>
    </div>

    <div class="bg-card-bg rounded-2xl shadow-sm dark:shadow-[0_4px_24px_rgba(0,0,0,0.4)] border border-border-color overflow-hidden transition-colors duration-300">
        
        <div class="flex border-b border-border-color bg-gray-50/50 dark:bg-gray-800/30 px-2 sm:px-6 pt-2">
            <button @click="activeTab = 'light'" 
                    :class="activeTab === 'light' ? 'border-primary text-primary bg-card-bg shadow-[0_-2px_10px_rgba(0,0,0,0.03)]' : 'border-transparent text-text-muted hover:text-text-main hover:bg-background/80'" 
                    class="flex items-center gap-2 px-6 py-3.5 text-sm font-semibold transition-all duration-200 border-b-2 rounded-t-xl outline-none relative top-[1px]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path></svg>
                <span>{{ __('messages.light_mode') ?? 'Light Mode' }}</span>
            </button>
            <button @click="activeTab = 'dark'" 
                    :class="activeTab === 'dark' ? 'border-primary text-primary bg-card-bg shadow-[0_-2px_10px_rgba(0,0,0,0.03)]' : 'border-transparent text-text-muted hover:text-text-main hover:bg-background/80'" 
                    class="flex items-center gap-2 px-6 py-3.5 text-sm font-semibold transition-all duration-200 border-b-2 rounded-t-xl outline-none ml-2 relative top-[1px]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path></svg>
                <span>{{ __('messages.dark_mode') ?? 'Dark Mode' }}</span>
            </button>
        </div>

        <form wire:submit.prevent="saveTheme" class="p-6 sm:p-8 bg-card-bg">
            
            @php
                $labels = [
                    'primary' => __('messages.primary_brand_color') ?? 'Primary Brand Color', 
                    'primary_text' => __('messages.text_on_primary') ?? 'Text on Primary',
                    'background' => __('messages.main_background') ?? 'Main Background', 
                    'card_bg' => __('messages.card_background') ?? 'Card Background',
                    'header' => __('messages.header_background') ?? 'Header Background', 
                    'sidebar' => __('messages.sidebar_background') ?? 'Sidebar Background',
                    'dropdown' => __('messages.dropdown_background') ?? 'Dropdown Background', 
                    'border_color' => __('messages.borders_and_lines') ?? 'Borders & Lines',
                    'text_main' => __('messages.main_text_color') ?? 'Main Text Color', 
                    'text_muted' => __('messages.muted_secondary_text') ?? 'Muted/Secondary Text'
                ];
            @endphp

            <div x-show="activeTab === 'light'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-8 gap-y-6">
                    @foreach($labels as $key => $label)
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-text-muted">{{ $label }}</label>
                            <div class="flex items-center gap-3 bg-gray-50/50 dark:bg-gray-800/30 p-1.5 rounded-xl border border-gray-100 dark:border-gray-700/50 hover:border-gray-200 dark:hover:border-gray-600 transition-colors">
                                <div class="relative w-10 h-10 rounded-lg overflow-hidden shadow-sm border border-border-color cursor-pointer flex-shrink-0 transition-transform hover:scale-105">
                                    <input type="color" wire:model.live="lightColors.{{ $key }}" class="absolute -top-4 -left-4 w-20 h-20 cursor-pointer">
                                </div>
                                <input type="text" wire:model.live="lightColors.{{ $key }}" class="w-full bg-transparent border-none text-text-main px-3 font-mono text-sm focus:ring-0 uppercase placeholder-gray-400">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div x-show="activeTab === 'dark'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-8 gap-y-6">
                    @foreach($labels as $key => $label)
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-text-muted">{{ $label }}</label>
                            <div class="flex items-center gap-3 bg-gray-50/50 dark:bg-gray-800/30 p-1.5 rounded-xl border border-gray-100 dark:border-gray-700/50 hover:border-gray-200 dark:hover:border-gray-600 transition-colors">
                                <div class="relative w-10 h-10 rounded-lg overflow-hidden shadow-sm border border-border-color cursor-pointer flex-shrink-0 transition-transform hover:scale-105">
                                    <input type="color" wire:model.live="darkColors.{{ $key }}" class="absolute -top-4 -left-4 w-20 h-20 cursor-pointer">
                                </div>
                                <input type="text" wire:model.live="darkColors.{{ $key }}" class="w-full bg-transparent border-none text-text-main px-3 font-mono text-sm focus:ring-0 uppercase placeholder-gray-400">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-border-color flex items-center justify-end gap-4">
                <button type="submit" class="relative flex items-center gap-2 px-8 py-2.5 rounded-xl font-bold text-sm text-primary-text bg-primary shadow-sm hover:shadow-md hover:brightness-110 active:scale-[0.98] transition-all focus:outline-none focus:ring-2 focus:ring-primary/50 overflow-hidden group">
                    <div class="absolute inset-0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    <span wire:loading.remove wire:target="saveTheme">{{ __('messages.save_changes') ?? 'Save Changes' }}</span>
                    <span wire:loading wire:target="saveTheme">{{ __('messages.saving') ?? 'Saving...' }}</span>
                </button>
            </div>
            
        </form>
    </div>
</div>

<style>
    @keyframes shimmer { 100% { transform: translateX(100%); } }
    
    /* Remove default focus ring from nested input */
    input[type="text"]:focus {
        outline: none;
        box-shadow: none;
    }
</style>