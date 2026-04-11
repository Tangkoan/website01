@if($isModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 dark:bg-black/60 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-3xl rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden flex flex-col max-h-[90vh]">
            
            {{-- Header --}}
            <div class="p-5 border-b border-[var(--color-border-color)] flex justify-between items-center bg-[var(--color-background)]/50 shrink-0">
                <h3 class="text-lg font-bold text-[var(--color-text-main)]">
                    {{ $itemId ? __('messages.edit') : __('messages.add_new') }} {{ __('messages.sidebar') }}
                </h3>
                <button type="button" wire:click="$set('isModalOpen', false)" class="text-[var(--color-text-muted)] hover:text-red-500 font-bold text-2xl transition-colors">&times;</button>
            </div>
            
            {{-- Form Body --}}
            <form wire:submit.prevent="saveItem" class="flex-1 flex flex-col overflow-hidden">
                <div class="p-6 overflow-y-auto no-scrollbar space-y-5">
                    
                    {{-- 1. Parent Selector --}}
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4">
                        <label class="w-full md:w-1/4 text-xs font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.parent_id') }}</label>
                        <div class="w-full md:w-3/4">
                            <select wire:model="parent_id" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none cursor-pointer">
                                <option value="">Select Parent...</option>
                                @php $opts = \App\Models\Sidebar::whereNull('parent_id')->get(); @endphp
                                @foreach($opts as $opt) <option value="{{ $opt->id }}">{{ $opt->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- 2. Name & URL --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b border-[var(--color-border-color)] pb-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.name') }}</label>
                            <input type="text" wire:model="name" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none" placeholder="Enter Name...">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.url') }}</label>
                            <input type="text" wire:model="url" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none" placeholder="e.g. settings/users">
                        </div>
                    </div>

                    {{-- 🌟 3. Smart Icon Picker (Fixed Empty Boxes & Save Logic) --}}
                    <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4" 
                         x-data="{ 
                            showPicker: false, 
                            search: '', 
                            icons: [], 
                            loading: false,
                            iconValue: @entangle('icon').live, // ✅ ប្រើ .live ដើម្បីឱ្យ Livewire ដឹងភ្លាមៗ
                            
                            searchIcons() {
                                if (this.search.length < 2) return;
                                this.loading = true;
                                fetch(`https://api.iconify.design/search?query=${this.search}&limit=40`)
                                    .then(res => res.json())
                                    .then(data => { this.icons = data.icons; this.loading = false; });
                            },
                            
                            selectIcon(name) {
                                this.iconValue = name;
                                $wire.set('icon', name); // ✅ បង្ខំឱ្យ Livewire Update តម្លៃដើម្បីកុំឱ្យ Save អត់ចូល
                                this.showPicker = false;
                            }
                         }">
                        
                        <label class="w-full md:w-1/4 text-xs font-black text-[var(--color-text-muted)] uppercase tracking-widest md:pt-3">{{ __('messages.icon') }}</label>
                        
                        <div class="w-full md:w-3/4 relative">
                            <div class="flex gap-2">
                                {{-- Preview Button --}}
                                <button type="button" @click="showPicker = !showPicker" 
                                        class="w-11 h-11 flex items-center justify-center bg-[var(--color-background)] border border-[var(--color-border-color)] rounded-lg text-2xl hover:border-[var(--color-primary)] transition-all shrink-0 text-[var(--color-text-main)]">
                                    
                                    <template x-if="iconValue && iconValue.includes('<svg')">
                                        <span x-html="iconValue" class="flex items-center justify-center"></span>
                                    </template>
                                    
                                    {{-- ✅ កែមកប្រើ <iconify-icon> វិញ សម្រាប់ Preview --}}
                                    <template x-if="iconValue && !iconValue.includes('<svg')">
                                        <iconify-icon :icon="iconValue"></iconify-icon>
                                    </template>
                                    
                                    <template x-if="!iconValue">
                                        <span class="text-[10px] font-bold text-[var(--color-text-muted)]">ICON</span>
                                    </template>
                                </button>
                                
                                <input type="text" x-model="iconValue" class="flex-1 h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none" placeholder="e.g. lucide:settings">
                            </div>

                            {{-- Dropdown Search Picker --}}
                            <div x-show="showPicker" @click.away="showPicker = false" x-cloak
                                 class="absolute left-0 top-12 z-[110] w-full bg-[var(--color-card-bg)] border border-[var(--color-border-color)] shadow-2xl rounded-xl p-4 animate-in zoom-in-95 duration-200">
                                
                                <div class="relative mb-3">
                                    <input type="text" x-model="search" @input.debounce.400ms="searchIcons()" 
                                           class="w-full h-10 bg-[var(--color-bg-sub)] border border-[var(--color-border-color)] rounded-lg px-10 text-xs text-[var(--color-text-main)] outline-none focus:border-[var(--color-primary)]" 
                                           placeholder="Search 200,000+ icons (e.g. home, user, cart)...">
                                    <svg class="absolute left-3 top-3 w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <span x-show="loading" class="absolute right-3 top-3"><svg class="animate-spin h-4 w-4 text-[var(--color-primary)]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                                </div>

                                <div class="grid grid-cols-8 gap-2 overflow-y-auto max-h-52 no-scrollbar p-1">
                                    <template x-for="iconName in icons" :key="iconName">
                                        <button type="button" @click="selectIcon(iconName)" 
                                                class="p-2 flex items-center justify-center hover:bg-[var(--color-primary)] hover:text-white rounded-lg border border-[var(--color-border-color)] transition-all text-[var(--color-text-main)]">
                                            
                                            {{-- ✅ កែមកប្រើ <iconify-icon> វិញទើបរូបលោតចេញមកក្នុង Dropdown --}}
                                            <iconify-icon :icon="iconName" class="text-xl"></iconify-icon>
                                            
                                        </button>
                                    </template>
                                    <div x-show="icons.length === 0 && !loading" class="col-span-8 text-center py-4 text-[var(--color-text-muted)] text-[10px] font-black uppercase tracking-widest">
                                        Type to search icons...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Permission & Order --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b border-[var(--color-border-color)] pb-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.permission') }}</label>
                            <input type="text" wire:model="permission" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-sm rounded-lg px-4 focus:ring-2 focus:ring-[var(--color-primary)] outline-none" placeholder="view-sidebar">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.order') }}</label>
                            <input type="number" wire:model="order" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-sm rounded-lg px-4 focus:ring-2 focus:ring-[var(--color-primary)] outline-none" placeholder="10">
                        </div>
                    </div>

                    {{-- 5. Status Toggle --}}
                    <div class="flex items-center justify-between p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)]">
                        <label class="text-xs font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.is_active') }}</label>
                        <div class="flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-[var(--color-primary)] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                            <span class="text-sm font-bold text-[var(--color-text-main)] uppercase">{{ $is_active ? __('messages.active') : __('messages.inactive') }}</span>
                        </div>
                    </div>

                </div>
                
                {{-- Footer Buttons --}}
                <div class="p-5 flex justify-end gap-3 border-t border-[var(--color-border-color)] bg-[var(--color-background)]/30 shrink-0">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-5 py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-bold rounded-lg text-xs hover:brightness-95 transition-all shadow-sm">CANCEL</button>
                    <button type="submit" class="px-5 py-2.5 bg-[var(--color-primary)] text-white font-bold rounded-lg shadow-md text-xs hover:brightness-110 transition-all flex items-center gap-2">
                        <span wire:loading.remove>SAVE DATA</span>
                        <span wire:loading class="flex items-center gap-2 italic">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif