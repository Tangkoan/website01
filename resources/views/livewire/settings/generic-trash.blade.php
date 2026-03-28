<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    
    {{-- Header --}}
    <div class="flex justify-between items-start sm:items-center gap-3 md:gap-4">
        
        {{-- Title Section --}}
        <h2 class="flex-1 min-w-0 text-xl md:text-3xl font-black text-[var(--color-text-main)] tracking-tight flex items-center gap-2.5 md:gap-3">
            <span class="shrink-0 p-2 md:p-2.5 bg-red-500/10 rounded-lg md:rounded-xl text-red-500 text-lg md:text-2xl flex items-center justify-center">
                🗑️
            </span>
            {{-- ប្រើ truncate បើចង់ឲ្យវាកាត់ជាសញ្ញា ... ពេលអក្សរវែងពេក ឬលុប truncate ចោលបើចង់ឲ្យវាធ្លាក់ចុះបន្ទាត់ --}}
            <span class="leading-tight truncate">{{ $title }}</span>
        </h2>

        {{-- Back Button --}}
        <a href="{{ route($backRoute) }}" wire:navigate class="shrink-0 p-2 sm:px-4 sm:py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-sm font-black text-[var(--color-text-main)] hover:brightness-95 flex items-center gap-2 transition-all focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/30 mt-0.5 sm:mt-0">
            <svg class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{-- លាក់អក្សរ Back លើ Mobile និងបង្ហាញវិញលើអក្រង់ធំ (sm:block) --}}
            <span class="hidden sm:block">{{ __('messages.back') ?? 'Back' }}</span>
        </a>

    </div>

    {{-- Filters & Bulk Actions --}}
    <div class="bg-[var(--color-card-bg)] p-3 rounded-xl border border-[var(--color-border-color)] shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:w-96 group">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[var(--color-text-muted)] group-focus-within:text-[var(--color-primary)] transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg pl-10 pr-4 text-sm font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 focus:border-[var(--color-primary)] outline-none transition-all" 
                placeholder="{{ __('messages.search_deleted') ?? 'Search deleted items...' }}">
        </div>

        @if(count($selectedItems) > 0)
            <div class="w-full sm:w-auto flex flex-wrap sm:flex-nowrap items-center justify-between gap-2 p-2 bg-[var(--color-primary)]/10 rounded-lg border border-[var(--color-primary)]/20 animate-in fade-in zoom-in-95 duration-200">
                <span class="text-sm font-black text-[var(--color-primary)] px-2">{{ count($selectedItems) }} Selected</span>
                <div class="flex items-center gap-2">
                    <button wire:click="restore()" class="px-4 py-2 bg-green-500 hover:bg-green-600 active:bg-green-700 text-white text-xs font-black rounded-md transition-colors shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Restore
                    </button>
                    <button wire:click="confirmForceDelete()" class="px-4 py-2 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white text-xs font-black rounded-md transition-colors shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden md:block bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm overflow-hidden min-h-[300px] relative">
        
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[var(--color-background)]/50 border-b border-[var(--color-border-color)]">
                    <th class="p-4 w-16 text-center">
                        <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/30 cursor-pointer">
                    </th>
                    <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">Information</th>
                    <th class="p-4 text-[10px] font-black text-red-500 uppercase tracking-widest text-center">Deleted At</th>
                    <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            
            {{-- មុខងារ Drag-to-select ប្រើ Alpine.js --}}
            <tbody x-data="{ isMouseDown: false, checkStatus: true }" 
                   @mousedown="isMouseDown = true" 
                   @mouseup.window="isMouseDown = false"
                   class="divide-y divide-[var(--color-border-color)]">
                
                @forelse($items as $item)
                    <tr wire:key="desktop-{{ $item->id }}" 
                        @mousedown="if(!$event.target.closest('button') && !$event.target.closest('label') && $event.target.tagName !== 'INPUT') { let cb = $el.querySelector('input[type=checkbox]'); if(cb) { checkStatus = !cb.checked; cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }"
                        @mouseenter="if(isMouseDown && !$event.target.closest('button') && !$event.target.closest('label')) { let cb = $el.querySelector('input[type=checkbox]'); if(cb && cb.checked !== checkStatus) { cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }"
                        class="hover:bg-[var(--color-background)]/50 transition-all group cursor-pointer select-none">
                        
                        <td class="p-4 text-center">
                            <input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] cursor-pointer">
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                @if($type === 'user')
                                    @if($item->image)
                                        <img src="{{ asset('storage/profile-photos/' . $item->image) }}" class="h-10 w-10 rounded-full object-cover border border-[var(--color-border-color)] shadow-sm">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-[var(--color-primary)]/10 flex items-center justify-center font-black text-[var(--color-primary)] text-xs border border-[var(--color-primary)]/20">{{ substr($item->name, 0, 1) }}</div>
                                    @endif
                                @endif
                                <div>
                                    <div class="font-black text-[var(--color-text-main)] group-hover:text-[var(--color-primary)] transition-colors">{{ $item->name }}</div>
                                    <div class="text-[10px] text-[var(--color-text-muted)] font-bold">
                                        {{ $type === 'user' ? $item->email : 'Guard: ' . ($item->guard_name ?? 'web') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            <div class="text-red-500 font-bold text-sm">{{ $item->deleted_at->format('d M, Y') }}</div>
                            <div class="text-[10px] text-[var(--color-text-muted)] font-black uppercase">{{ $item->deleted_at->format('h:i A') }}</div>
                        </td>
                        <td class="p-4 flex justify-end gap-2">
                            <button wire:click="restore({{ $item->id }})" title="Restore" class="p-2 rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-all shadow-sm border border-green-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                            <button wire:click="confirmForceDelete({{ $item->id }})" title="Delete Permanently" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm border border-red-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-16 text-center text-[var(--color-text-muted)] uppercase text-xs italic opacity-50">{{ __('messages.no_deleted_data') ?? 'No deleted data found' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile View --}}
    <div class="md:hidden space-y-3">
        @forelse($items as $item)
            <div wire:key="mobile-{{ $item->id }}" class="bg-[var(--color-card-bg)] p-3.5 rounded-xl border border-[var(--color-border-color)] shadow-sm space-y-3 transition-all">
                
                {{-- ផ្នែកខាងលើ: Checkbox និង ព័ត៌មាន --}}
                <div class="flex items-start gap-3">
                    <div class="mt-0.5">
                        <input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" class="w-4 h-4 rounded text-[var(--color-primary)] border-2 border-[var(--color-text-main)] cursor-pointer">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-[var(--color-text-main)] text-sm truncate">{{ $item->name }}</div>
                        <div class="text-[11px] font-bold text-[var(--color-text-muted)] truncate">{{ $type === 'user' ? $item->email : $item->guard_name }}</div>
                    </div>
                </div>

                <div class="border-t border-dashed border-[var(--color-border-color)] w-full"></div>

                {{-- ផ្នែកខាងក្រោម: កាលបរិច្ឆេទ និង Icon ប៊ូតុង --}}
                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-red-500 uppercase tracking-widest">Deleted At</span>
                        <span class="text-xs font-bold text-[var(--color-text-muted)]">{{ $item->deleted_at->format('d M, Y') }}</span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        {{-- Icon Restore (ប្រើសញ្ញាព្រួញបង្វិលត្រឡប់) --}}
                        <button wire:click="restore({{ $item->id }})" class="p-2.5 bg-green-50 text-green-600 rounded-lg border border-green-100 shadow-sm active:bg-green-600 active:text-white transition-all focus:outline-none focus:ring-2 focus:ring-green-500/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </button>
                        
                        {{-- Icon Delete Forever (ប្រើសញ្ញាធុងសំរាម) --}}
                        <button wire:click="confirmForceDelete({{ $item->id }})" class="p-2.5 bg-red-50 text-red-600 rounded-lg border border-red-100 shadow-sm active:bg-red-600 active:text-white transition-all focus:outline-none focus:ring-2 focus:ring-red-500/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
                
            </div>
        @empty
             <div class="bg-[var(--color-card-bg)] p-10 rounded-xl border border-[var(--color-border-color)] text-center flex flex-col items-center justify-center space-y-2">
                 <span class="text-3xl opacity-50">📂</span>
                 <span class="text-[var(--color-text-muted)] text-sm font-bold opacity-70">{{ __('messages.no_deleted_data') ?? 'No deleted data found' }}</span>
             </div>
        @endforelse
    </div>
    
    <div class="mt-4">{{ $items->links('livewire.parts.pagination') }}</div>

    {{-- Modal Confirm Delete --}}
    <x-modals.delete 
        :isOpen="$isDeleteModalOpen" 
        onClose="$set('isDeleteModalOpen', false)" 
        onConfirm="executeDelete" 
        message="{{ $deleteId ? 'This item will be permanently deleted.' : 'All selected items will be permanently removed from the system.' }}" 
    />
</div>