@if($isBulkEditModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 dark:bg-black/60 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-4xl rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden flex flex-col md:flex-row max-h-[90vh]">
            
            {{-- ផ្នែកខាងឆ្វេង: បញ្ជី Items ដែលត្រូវ Edit (មាន Scroll ដាច់ដោយឡែក) --}}
            <div class="w-full md:w-1/3 bg-[var(--color-background)] border-r border-[var(--color-border-color)] flex flex-col max-h-[35vh] md:max-h-full">
                <div class="p-5 border-b border-[var(--color-border-color)] shrink-0 bg-[var(--color-background)]">
                    <h3 class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.editing') ?? 'Editing' }} {{ count($selectedItemsQueue) }} {{ __('messages.items') ?? 'Items' }}</h3>
                </div>
                <div class="p-3 overflow-y-auto flex-1 space-y-2 no-scrollbar">
                    @foreach($selectedItemsQueue as $index => $id)
                        @php $itemName = \App\Models\Sidebar::find($id)?->name ?? 'Unknown'; @endphp
                        <button type="button" wire:key="bulk-item-{{ $index }}" wire:click="jumpToBulkItem({{ $index }})" class="w-full text-left p-2.5 md:p-3 rounded-lg font-bold text-xs md:text-sm transition-all flex items-center justify-between {{ $currentBulkIndex === $index ? 'bg-[var(--color-primary)] text-[var(--color-primary-text)] shadow-md' : 'bg-[var(--color-card-bg)] text-[var(--color-text-main)] border border-[var(--color-border-color)] hover:border-[var(--color-primary)]/50' }}">
                            <span class="truncate pr-2">{{ $itemName }}</span>
                            @if($currentBulkIndex === $index) <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg> @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- ផ្នែកខាងស្តាំ: Form សម្រាប់ Edit (មាន Scroll ត្រឹមតែតួ Form ប៉ុណ្ណោះ) --}}
            <div class="w-full md:w-2/3 flex flex-col bg-[var(--color-card-bg)] max-h-[55vh] md:max-h-full overflow-hidden">
                {{-- Header របស់ Form --}}
                <div class="p-5 md:p-6 border-b border-[var(--color-border-color)] flex justify-between items-center shrink-0">
                    <h3 class="text-lg md:text-xl font-black text-[var(--color-text-main)]">{{ __('messages.edit') ?? 'Edit' }} <span class="text-[var(--color-primary)]">({{ $currentBulkIndex + 1 }}/{{ count($selectedItemsQueue) }})</span></h3>
                    <button type="button" wire:click="closeBulkEdit" class="text-[var(--color-text-muted)] hover:text-red-500 transition-colors bg-[var(--color-background)] rounded-full p-1.5"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                {{-- តួ Form --}}
                <form wire:submit.prevent="saveAndNextBulkItem" class="flex-1 overflow-y-auto p-5 md:p-6 flex flex-col">
                    <div class="space-y-4 flex-1">
                        {{-- Parent Selector --}}
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                            <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.parent_id') ?? 'Parent' }}</label>
                            <div class="w-full md:w-3/4">
                                <select wire:model="bulkItem_parent_id" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none cursor-pointer">
                                    <option value="">Select Parent...</option>
                                    @php $opts = class_exists('\App\Models\Sidebar') ? \App\Models\Sidebar::limit(100)->get() : collect(); @endphp
                                    @foreach($opts as $opt) <option value="{{ $opt->id }}">{{ $opt->name ?? $opt->title ?? 'ID: ' . $opt->id }}</option> @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Name --}}
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                            <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.name') ?? 'Name' }}</label>
                            <div class="w-full md:w-3/4">
                                <input type="text" wire:model="bulkItem_name" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Name...">
                                @error('bulkItem_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- URL --}}
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                            <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.url') ?? 'Url' }}</label>
                            <div class="w-full md:w-3/4">
                                <input type="text" wire:model="bulkItem_url" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Url...">
                                @error('bulkItem_url') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- 🌟 Icon Picker (Smart Tool បម្លែងមកពី Modal Form ធម្មតា) --}}
                        <div class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0" 
                             x-data="{ 
                                showPicker: false, 
                                search: '', 
                                icons: [], 
                                loading: false,
                                iconValue: @entangle('bulkItem_icon').live, // ✅ ដូរមកប្រើ bulkItem_icon
                                
                                searchIcons() {
                                    if (this.search.length < 2) return;
                                    this.loading = true;
                                    fetch(`https://api.iconify.design/search?query=${this.search}&limit=40`)
                                        .then(res => res.json())
                                        .then(data => { this.icons = data.icons; this.loading = false; });
                                },
                                
                                selectIcon(name) {
                                    this.iconValue = name;
                                    $wire.set('bulkItem_icon', name); // ✅ បង្ខំ Livewire ឱ្យ Update
                                    this.showPicker = false;
                                }
                             }">
                            
                            <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider md:pt-3">{{ __('messages.icon') ?? 'Icon' }}</label>
                            
                            <div class="w-full md:w-3/4 relative">
                                <div class="flex gap-2">
                                    {{-- Preview Button --}}
                                    <button type="button" @click="showPicker = !showPicker" 
                                            class="w-11 h-11 flex items-center justify-center bg-[var(--color-background)] border border-[var(--color-border-color)] rounded-lg text-2xl hover:border-[var(--color-primary)] transition-all shrink-0 text-[var(--color-text-main)]">
                                        
                                        <template x-if="iconValue && iconValue.includes('<svg')">
                                            <span x-html="iconValue" class="flex items-center justify-center"></span>
                                        </template>
                                        
                                        <template x-if="iconValue && !iconValue.includes('<svg')">
                                            <iconify-icon :icon="iconValue"></iconify-icon>
                                        </template>
                                        
                                        <template x-if="!iconValue">
                                            <span class="text-[10px] font-bold text-[var(--color-text-muted)]">ICON</span>
                                        </template>
                                    </button>
                                    
                                    <input type="text" x-model="iconValue" class="flex-1 h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="e.g. lucide:settings">
                                </div>
                                @error('bulkItem_icon') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

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

                        {{-- Permission --}}
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                            <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.permission') ?? 'Permission' }}</label>
                            <div class="w-full md:w-3/4">
                                <input type="text" wire:model="bulkItem_permission" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Permission...">
                                @error('bulkItem_permission') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Order --}}
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 border-b border-[var(--color-border-color)] pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                            <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.order') ?? 'Order' }}</label>
                            <div class="w-full md:w-3/4">
                                <input type="text" wire:model="bulkItem_order" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors" placeholder="Enter Order...">
                                @error('bulkItem_order') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Is Active --}}
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 p-4 bg-[var(--color-background)]/30 rounded-xl border border-[var(--color-border-color)] mb-4 last:mb-0">
                            <label class="w-full md:w-1/4 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.is_active') ?? 'Is Active' }}</label>
                            <div class="w-full md:w-3/4 flex items-center gap-3">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="bulkItem_is_active" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-[var(--color-primary)] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                                </label>
                                <span class="text-sm font-bold text-[var(--color-text-main)] uppercase">{{ $bulkItem_is_active ? __('messages.active') : __('messages.inactive') }}</span>
                            </div>
                        </div>

                    </div>

                    {{-- Footer (ប៊ូតុង Save & Skip) --}}
                    <div class="mt-8 flex gap-3 pt-5 border-t border-[var(--color-border-color)] shrink-0">
                        <button type="button" wire:click="skipBulkItem" class="flex-1 h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-black rounded-lg hover:brightness-95 transition-all uppercase tracking-widest text-xs">{{ __('messages.skip') ?? 'Skip' }}</button>
                        <button type="submit" class="flex-[2] h-11 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-black rounded-lg shadow-xl hover:brightness-110 active:scale-95 transition-all uppercase tracking-widest text-xs flex items-center justify-center gap-2">
                            <span wire:loading.remove>{{ __('messages.save_next') ?? 'Save & Next' }}</span>
                            <span wire:loading>{{ __('messages.processing') ?? 'Saving...' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif