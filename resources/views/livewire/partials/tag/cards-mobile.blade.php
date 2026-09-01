<div class="md:hidden space-y-3">
    <div class="flex items-center gap-3 px-2 mb-1">
        <input type="checkbox" wire:model.live="selectAll" id="selectAllMobile-tag" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] bg-transparent cursor-pointer">
        <label for="selectAllMobile-tag" class="text-sm font-black text-[var(--color-text-main)]">{{ __('messages.select_all') ?? 'Select All' }}</label>
    </div>

    @forelse($items as $item)
        <div wire:key="tag-mobile-{{ $item->id }}" x-data @click="if(!$event.target.closest('button') && !$event.target.closest('a') && !$event.target.closest('label') && $event.target.tagName !== 'INPUT' && $event.target.tagName !== 'SVG' && $event.target.tagName !== 'PATH') { let cb = $el.querySelector('.row-checkbox'); if(cb && !cb.disabled) cb.click() }" class="bg-[var(--color-card-bg)] p-4 rounded-xl shadow-sm border border-[var(--color-border-color)] flex flex-col gap-3 cursor-pointer hover:bg-[var(--color-background)]/50 transition-all select-none">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" class="row-checkbox w-4 h-4 mt-1 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] bg-transparent cursor-pointer flex-shrink-0 disabled:opacity-50">
                    <div class="min-w-0 flex-1 grid grid-cols-2 gap-2">
                                            @if(in_array('name', $selectedColumns)) <div class="flex flex-col min-w-0"><span class="text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.name') }}</span><span class="text-xs font-bold text-[var(--color-text-main)] truncate block">{{ strip_tags($item->name) }}</span></div> @endif
                    @if(in_array('slug', $selectedColumns)) <div class="flex flex-col min-w-0"><span class="text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.slug') }}</span><span class="text-xs font-bold text-[var(--color-text-main)] truncate block">{{ strip_tags($item->slug) }}</span></div> @endif

                    </div>
                </div>
            </div>
            
            <div class="border-t border-dashed border-[var(--color-border-color)] w-full mt-2"></div>
            
            <div class="flex justify-between items-center mt-2">
                <div class="flex gap-2">
                    <x-auth-button permission="edit-tag" wire:click.stop="editItem({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed relative z-20">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </x-auth-button>
                    <x-auth-button permission="delete-tag" wire:click.stop="confirmDelete({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed relative z-20">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </x-auth-button>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-[var(--color-card-bg)] p-8 rounded-xl border border-[var(--color-border-color)] text-center text-[var(--color-text-muted)] font-black uppercase tracking-widest text-xs italic">{{ __('messages.no_data') ?? 'No Data Found' }}</div>
    @endforelse

    <div class="w-full flex justify-center mt-4">{{ $items->links('livewire.parts.pagination') }}</div>
</div>