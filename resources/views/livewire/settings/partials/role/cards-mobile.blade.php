<div class="md:hidden space-y-3">
    @canany(['bulk-edit-permission', 'bulk-delete-permission'])
    <div class="flex items-center gap-3 px-2 mb-1">
        <input type="checkbox" wire:model.live="selectAll" id="selectAllMobile" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] bg-transparent cursor-pointer">
        <label for="selectAllMobile" class="text-sm font-black text-[var(--color-text-main)]">{{ __('messages.select_all') ?? 'Select All' }}</label>
    </div>
    @endcanany

    @forelse($roles as $item)
        <div wire:key="mobile-row-{{ $item->id }}" x-data @click="if(!$event.target.closest('button') && $event.target.tagName !== 'INPUT') $el.querySelector('input[type=checkbox]').click()" class="bg-[var(--color-card-bg)] p-4 rounded-xl shadow-sm border border-[var(--color-border-color)] flex flex-col gap-3 cursor-pointer hover:bg-[var(--color-background)]/50 transition-all">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    @canany(['bulk-edit-permission', 'bulk-delete-permission'])
                    <input type="checkbox" wire:model.live="selectedRoles" value="{{ $item->id }}" class="w-4 h-4 mt-1 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] bg-transparent cursor-pointer flex-shrink-0">
                    @endcanany
                    <div class="min-w-0">
                        @if(in_array('name', $selectedColumns))
                            <h4 class="font-black text-[var(--color-text-main)] text-base truncate">{{ $item->name }}</h4>
                        @endif
                        
                        <div class="flex items-center gap-2 mt-1">
                            {{-- បង្ហាញ Level --}}
                            @if(in_array('level', $selectedColumns))
                                <span class="px-2 py-0.5 bg-amber-500/10 text-amber-500 rounded-md text-[10px] font-black uppercase tracking-widest">Lv. {{ $item->level ?? 1 }}</span>
                            @endif

                            @if(in_array('guard_name', $selectedColumns))
                                <span class="px-2 py-0.5 bg-[var(--color-background)] border border-[var(--color-text-main)] rounded-md text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ $item->guard_name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            @canany(['edit-permission', 'delete-permission'])
            <div class="border-t border-dashed border-[var(--color-border-color)] w-full"></div>
            <div class="flex justify-between items-center">
                @if(in_array('created_at', $selectedColumns))
                    <span class="text-[10px] font-bold text-[var(--color-text-muted)] uppercase tracking-widest">{{ $item->created_at->format('d M, Y') }}</span>
                @else
                    <span></span>
                @endif
                <div class="flex gap-2">
                    @can('edit-permission') 
                    <button wire:click="managePermissions({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    </button>
                    @endcan

                    @can('edit-permission')
                    <button wire:click="editRole({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>
                    @endcan
                    @can('delete-permission')
                    <button wire:click="confirmDelete({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                    @endcan
                </div>
            </div>
            @endcanany
        </div>
    @empty
        <div class="bg-[var(--color-card-bg)] p-8 rounded-xl border border-[var(--color-border-color)] text-center text-[var(--color-text-muted)] font-black uppercase tracking-widest text-xs italic">{{ __('messages.no_data') ?? 'No Data' }}</div>
    @endforelse

    <div class="w-full flex justify-center mt-4">
        {{ $roles->links('livewire.parts.pagination') }}
    </div>
</div>