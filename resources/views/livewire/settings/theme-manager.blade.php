<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" 
     x-data="{ 
        activeTab: 'light',
        
        light: @entangle('lightColors').live,
        dark: @entangle('darkColors').live,

        applyLivePreview() {
            let isDark = document.documentElement.classList.contains('dark');
            let currentColors = isDark ? this.dark : this.light;

            for (const [key, value] of Object.entries(currentColors)) {
                let cssKey = '--color-' + key.replace(/_/g, '-');
                if (key === 'blur') {
                    cssKey = '--theme-blur';
                }
                document.documentElement.style.setProperty(cssKey, value);
            }
        }
     }"
     x-init="
        $nextTick(() => {
            $watch('light', () => applyLivePreview());
            $watch('dark', () => applyLivePreview());
            
            const observer = new MutationObserver(() => applyLivePreview());
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            applyLivePreview();
        });
     "
>

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-[var(--color-text-main)] tracking-tight flex items-center gap-2">
                <span class="text-3xl">🎨</span>
                {{ __('messages.theme_customization') }}
            </h2>
        </div>
    </div>

    <div class="bg-[var(--color-card-bg)] rounded-2xl shadow-sm border border-[var(--color-border-color)] overflow-hidden transition-colors duration-300">
        
        {{-- Tabs --}}
        <div class="flex border-b border-[var(--color-border-color)] bg-gray-50/50 dark:bg-gray-800/30 px-2 sm:px-6 pt-2">
            <button @click="activeTab = 'light'" 
                    :class="activeTab === 'light' ? 'border-[var(--color-primary)] text-[var(--color-primary)] bg-[var(--color-card-bg)] shadow-[0_-2px_10px_rgba(0,0,0,0.03)]' : 'border-transparent text-[var(--color-text-muted)] hover:text-[var(--color-text-main)] hover:bg-[var(--color-background)]/80'" 
                    class="flex items-center gap-2 px-6 py-3.5 text-sm font-semibold transition-all duration-200 border-b-2 rounded-t-xl outline-none relative top-[1px]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path></svg>
                <span>{{ __('messages.light_mode') ?? 'Light Mode' }}</span>
            </button>
            <button @click="activeTab = 'dark'" 
                    :class="activeTab === 'dark' ? 'border-[var(--color-primary)] text-[var(--color-primary)] bg-[var(--color-card-bg)] shadow-[0_-2px_10px_rgba(0,0,0,0.03)]' : 'border-transparent text-[var(--color-text-muted)] hover:text-[var(--color-text-main)] hover:bg-[var(--color-background)]/80'" 
                    class="flex items-center gap-2 px-6 py-3.5 text-sm font-semibold transition-all duration-200 border-b-2 rounded-t-xl outline-none ml-2 relative top-[1px]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path></svg>
                <span>{{ __('messages.dark_mode') ?? 'Dark Mode' }}</span>
            </button>
        </div>

        <form wire:submit.prevent="saveTheme" class="p-6 sm:p-8 bg-[var(--color-card-bg)]">
            
            @php
                $labels = [
                    'primary' => __('messages.primary_brand_color'), 
                    'primary_text' => __('messages.text_on_primary'),
                    'background' => __('messages.main_background'), 
                    'card_bg' => __('messages.card_background'),
                    'header' => __('messages.header_background'), 
                    'sidebar' => __('messages.sidebar_background'),
                    'dropdown' => __('messages.dropdown_background'), 
                    'border_color' => __('messages.borders_and_lines'),
                    'text_main' => __('messages.main_text_color'), 
                    'text_muted' => __('messages.muted_secondary_text'),
                    'blur' => __('messages.backdrop_blur')
                ];
            @endphp

            {{-- ================= LIGHT MODE ================= --}}
            <div x-show="activeTab === 'light'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-8 gap-y-6">
                    @foreach($labels as $key => $label)
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--color-text-muted)]">{{ $label }}</label>
                            
                            @if($key !== 'blur')
                                {{-- ✅ កែប្រែ៖ បោះទិន្នន័យពណ៌ពី PHP ទៅឱ្យ Alpine ដោយផ្ទាល់ --}}
                                <div x-data="colorPicker('light', '{{ $key }}', '{{ $lightColors[$key] ?? '' }}')" class="space-y-3 p-3 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)]">
                                    <div class="flex items-center gap-3">
                                        <div class="relative w-12 h-12 rounded-lg overflow-hidden shadow-sm border border-[var(--color-border-color)] flex-shrink-0 checkerboard-bg transition-transform hover:scale-105">
                                            <div class="absolute inset-0 pointer-events-none" :style="`background-color: ${rgbaString}`"></div>
                                            <input type="color" x-model="hexColor" @input="updateRgba()" class="absolute -top-4 -left-4 w-20 h-20 cursor-pointer opacity-0 z-10">
                                        </div>
                                        <div class="flex-1">
                                            <input type="text" x-model="rgbaString" @input="parseManualInput()" class="w-full bg-[var(--color-card-bg)] border border-[var(--color-border-color)] text-[var(--color-text-main)] px-3 py-2 rounded-lg font-mono text-sm focus:ring-2 focus:ring-[var(--color-primary)]/50 focus:border-[var(--color-primary)] outline-none transition-all">
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 px-1">
                                        <span class="text-[10px] font-bold text-[var(--color-text-muted)] uppercase tracking-wider w-12">Opacity</span>
                                        <input type="range" x-model="opacity" @input="updateRgba()" min="0" max="100" step="1" class="w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-[var(--color-primary)]">
                                        <span class="text-xs font-mono text-[var(--color-text-main)] w-8 text-right" x-text="`${opacity}%`"></span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-3 bg-[var(--color-background)]/50 p-2.5 rounded-xl border border-[var(--color-border-color)]">
                                    <div class="w-10 h-10 rounded-lg bg-[var(--color-card-bg)] flex items-center justify-center border border-[var(--color-border-color)] flex-shrink-0">
                                        <svg class="w-5 h-5 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <input type="text" wire:model.live.debounce.500ms="lightColors.{{ $key }}" class="w-full bg-transparent border-none text-[var(--color-text-main)] px-2 font-mono text-sm focus:ring-0 placeholder-gray-400">
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ================= DARK MODE ================= --}}
            <div x-show="activeTab === 'dark'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-8 gap-y-6">
                    @foreach($labels as $key => $label)
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--color-text-muted)]">{{ $label }}</label>
                            
                            @if($key !== 'blur')
                                {{-- ✅ កែប្រែ៖ បោះទិន្នន័យពណ៌ពី PHP ទៅឱ្យ Alpine ដោយផ្ទាល់ --}}
                                <div x-data="colorPicker('dark', '{{ $key }}', '{{ $darkColors[$key] ?? '' }}')" class="space-y-3 p-3 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)]">
                                    <div class="flex items-center gap-3">
                                        <div class="relative w-12 h-12 rounded-lg overflow-hidden shadow-sm border border-[var(--color-border-color)] flex-shrink-0 checkerboard-bg transition-transform hover:scale-105">
                                            <div class="absolute inset-0 pointer-events-none" :style="`background-color: ${rgbaString}`"></div>
                                            <input type="color" x-model="hexColor" @input="updateRgba()" class="absolute -top-4 -left-4 w-20 h-20 cursor-pointer opacity-0 z-10">
                                        </div>
                                        <div class="flex-1">
                                            <input type="text" x-model="rgbaString" @input="parseManualInput()" class="w-full bg-[var(--color-card-bg)] border border-[var(--color-border-color)] text-[var(--color-text-main)] px-3 py-2 rounded-lg font-mono text-sm focus:ring-2 focus:ring-[var(--color-primary)]/50 focus:border-[var(--color-primary)] outline-none transition-all">
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 px-1">
                                        <span class="text-[10px] font-bold text-[var(--color-text-muted)] uppercase tracking-wider w-12">Opacity</span>
                                        <input type="range" x-model="opacity" @input="updateRgba()" min="0" max="100" step="1" class="w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-[var(--color-primary)]">
                                        <span class="text-xs font-mono text-[var(--color-text-main)] w-8 text-right" x-text="`${opacity}%`"></span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-3 bg-[var(--color-background)]/50 p-2.5 rounded-xl border border-[var(--color-border-color)]">
                                    <div class="w-10 h-10 rounded-lg bg-[var(--color-card-bg)] flex items-center justify-center border border-[var(--color-border-color)] flex-shrink-0">
                                        <svg class="w-5 h-5 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <input type="text" wire:model.live.debounce.500ms="darkColors.{{ $key }}" class="w-full bg-transparent border-none text-[var(--color-text-main)] px-2 font-mono text-sm focus:ring-0 placeholder-gray-400">
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-[var(--color-border-color)] flex items-center justify-end gap-4">
                <button type="submit" class="relative flex items-center gap-2 px-8 py-2.5 rounded-xl font-bold text-sm text-[var(--color-primary-text)] bg-[var(--color-primary)] shadow-sm hover:shadow-md hover:brightness-110 active:scale-[0.98] transition-all focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/50 overflow-hidden group">
                    <div class="absolute inset-0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    <span wire:loading.remove wire:target="saveTheme">{{ __('messages.save_changes') }}</span>
                    <span wire:loading wire:target="saveTheme">{{ __('messages.saving') }}</span>
                </button>
            </div>
            
        </form>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        // ✅ ត្រលប់មកប្រើទម្រង់ដើមវិញ (មិនបាច់បោះ initialColor ទេ)
        Alpine.data('colorPicker', (themeMode, colorKey) => ({
            hexColor: '#000000',
            opacity: 100,
            rgbaString: '',
            
            init() {
                // ✅ អាថ៌កំបាំងទី ១៖ រង់ចាំឱ្យ Livewire component ដើរស្រួលបួលសិន ទើបយើងទាញយកតម្លៃ
                this.$nextTick(() => {
                    // ទាញយកតម្លៃពី Livewire
                    let initialValue = themeMode === 'light' ? this.$wire.lightColors[colorKey] : this.$wire.darkColors[colorKey];
                    
                    // បើទាញបាន ទើបយកទៅ Parse (ដើម្បីការពារកុំឱ្យវាចេញ Error "undefined")
                    if (initialValue) {
                        this.rgbaString = initialValue;
                        this.parseInitialColor(initialValue);
                    }

                    // ✅ អាថ៌កំបាំងទី ២៖ ចាប់ផ្តើមតាមដានតម្លៃ (Watch) បន្ទាប់ពីទាញបានតម្លៃដំបូងរួច
                    this.$watch(`$wire.${themeMode}Colors.${colorKey}`, (val) => {
                        if (val && val !== this.rgbaString) {
                            this.rgbaString = val;
                            this.parseInitialColor(val);
                        }
                    });
                });
            },

            updateRgba() {
                let r = 0, g = 0, b = 0;
                let hex = this.hexColor.replace('#', '');
                if (hex.length === 3) {
                    r = parseInt(hex.charAt(0) + hex.charAt(0), 16);
                    g = parseInt(hex.charAt(1) + hex.charAt(1), 16);
                    b = parseInt(hex.charAt(2) + hex.charAt(2), 16);
                } else if (hex.length === 6) {
                    r = parseInt(hex.substring(0, 2), 16);
                    g = parseInt(hex.substring(2, 4), 16);
                    b = parseInt(hex.substring(4, 6), 16);
                }
                
                let alpha = (this.opacity / 100).toFixed(2);
                
                if (this.opacity == 100) {
                    this.rgbaString = this.hexColor;
                } else {
                    this.rgbaString = `rgba(${r}, ${g}, ${b}, ${alpha})`;
                }

                if(themeMode === 'light') {
                    this.$wire.lightColors[colorKey] = this.rgbaString;
                } else {
                    this.$wire.darkColors[colorKey] = this.rgbaString;
                }
            },

            parseManualInput() {
                this.parseInitialColor(this.rgbaString);
                
                if(themeMode === 'light') {
                    this.$wire.lightColors[colorKey] = this.rgbaString;
                } else {
                    this.$wire.darkColors[colorKey] = this.rgbaString;
                }
            },

            parseInitialColor(colorStr) {
                if (!colorStr) return;
                
                colorStr = colorStr.trim().toLowerCase();
                
                if (colorStr.startsWith('#')) {
                    this.hexColor = colorStr.substring(0, 7);
                    this.opacity = 100;
                } 
                else if (colorStr.startsWith('rgba')) {
                    let rgbaParams = colorStr.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([\d.]+))?\)/);
                    if (rgbaParams) {
                        let r = parseInt(rgbaParams[1]).toString(16).padStart(2, '0');
                        let g = parseInt(rgbaParams[2]).toString(16).padStart(2, '0');
                        let b = parseInt(rgbaParams[3]).toString(16).padStart(2, '0');
                        this.hexColor = `#${r}${g}${b}`;
                        
                        let a = rgbaParams[4] !== undefined ? parseFloat(rgbaParams[4]) : 1;
                        this.opacity = Math.round(a * 100);
                    }
                }
            }
        }));
    });
</script>

<style>
    @keyframes shimmer { 100% { transform: translateX(100%); } }
    
    .checkerboard-bg {
        background-color: #fff;
        background-image: 
            linear-gradient(45deg, #ccc 25%, transparent 25%, transparent 75%, #ccc 75%, #ccc),
            linear-gradient(45deg, #ccc 25%, transparent 25%, transparent 75%, #ccc 75%, #ccc);
        background-size: 10px 10px;
        background-position: 0 0, 5px 5px;
    }
</style>