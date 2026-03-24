<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    {{-- ផ្នែក Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-[var(--color-text-main)] tracking-tight flex items-center gap-3">
                <span class="p-2 bg-red-500/10 rounded-lg md:rounded-xl text-red-500 text-xl md:text-2xl">🗑️</span>
                {{ __('messages.permissions_trash') ?? 'Permissions Trash' }}
            </h2>
        </div>
        
        <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0 relative">
            <a href="/settings/permission" wire:navigate class="px-4 py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-black rounded-lg hover:brightness-95 transition-all text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('messages.back') ?? 'Back' }}
            </a>
        </div>
    </div>

    {{-- ផ្នែក Search និង Bulk Actions --}}
    <div class="bg-[var(--color-card-bg)] p-3 rounded-xl border border-[var(--color-border-color)] shadow-sm flex flex-col lg:flex-row items-center justify-between gap-4">
        <div class="relative w-full lg:w-96 group">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[var(--color-text-muted)] pointer-events-none">
                <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                class="w-full h-10 md:h-11 bg-[var(--color-background)] border border-transparent focus:border-[var(--color-primary)]/50 text-[var(--color-text-main)] rounded-lg pl-10 pr-4 text-sm font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 transition-all outline-none" 
                placeholder="{{ __('messages.search_deleted') ?? 'Search deleted data...' }}">
        </div>

        @if(count($selectedPermissions) > 0)
            <div class="flex flex-col sm:flex-row items-center gap-2 p-1.5 bg-[var(--color-primary)]/10 dark:bg-[var(--color-primary)]/5 rounded-lg border border-[var(--color-primary)]/20 animate-in slide-in-from-right-4 w-full lg:w-auto">
                <span class="text-xs font-black text-[var(--color-primary)] px-3 uppercase w-full sm:w-auto text-center py-1 sm:py-0">{{ count($selectedPermissions) }} {{ __('messages.selected') ?? 'Selected' }}</span>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button wire:click="restoreSelected" class="flex-1 sm:flex-none px-3 py-2 bg-green-500 text-white text-xs font-black rounded-md hover:bg-green-600 transition shadow-sm flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        {{ __('messages.restore') ?? 'Restore' }}
                    </button>
                    <button wire:click="confirmForceDelete()" class="flex-1 sm:flex-none px-3 py-2 bg-red-600 text-white text-xs font-black rounded-md hover:bg-red-700 transition shadow-sm flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        {{ __('messages.force_delete') ?? 'Force Delete' }}
                    </button>
                </div>
            </div>
        @endif
    </div>

    <div>
        {{-- ផ្នែក Desktop View (Table) --}}
        <div class="hidden md:block bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm min-h-[300px]">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[var(--color-background)]/50 border-b border-[var(--color-border-color)]">
                            <th class="p-4 w-16 text-center">
                                <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/30 bg-transparent dark:bg-[var(--color-background)] checked:bg-[var(--color-primary)] checked:border-[var(--color-primary)] transition-all cursor-pointer">
                            </th>
                            <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.name') ?? 'Name' }}</th>
                            <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center">{{ __('messages.guard') ?? 'Guard' }}</th>
                            <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center text-red-500">{{ __('messages.deleted_at') ?? 'Deleted At' }}</th>
                            <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-right">{{ __('messages.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody x-data="{ isMouseDown: false, checkStatus: true }" 
                        @mousedown="isMouseDown = true" 
                        @mouseup.window="isMouseDown = false"
                        class="divide-y divide-[var(--color-border-color)] relative">
                        
                        <div wire:loading wire:target="getTrashedPermissionsProperty" class="absolute inset-0 z-10 bg-[var(--color-card-bg)]/50 backdrop-blur-sm flex items-center justify-center">
                            <svg class="animate-spin w-8 h-8 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </div>

                        @forelse($permissions as $item)
                            <tr @mousedown="if(!$event.target.closest('button')) { let cb = $el.querySelector('input[type=checkbox]'); if(cb) { if($event.target.tagName !== 'INPUT') { checkStatus = !cb.checked; cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } else { checkStatus = !cb.checked; } } }" 
                                @mouseenter="if(isMouseDown && !$event.target.closest('button')) { let cb = $el.querySelector('input[type=checkbox]'); if(cb && cb.checked !== checkStatus) { cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }" 
                                class="hover:bg-[var(--color-background)]/50 transition-all group cursor-pointer select-none">
                                <td class="p-4 text-center">
                                    <input type="checkbox" wire:model.live="selectedPermissions" value="{{ $item->id }}" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/30 bg-transparent dark:bg-[var(--color-background)] checked:bg-[var(--color-primary)] checked:border-[var(--color-primary)] transition-all cursor-pointer">
                                </td>
                                <td class="p-4 font-black text-[var(--color-text-main)] line-through opacity-70 text-sm md:text-base">{{ $item->name }}</td>
                                <td class="p-4 text-center"><span class="px-3 py-1 bg-[var(--color-background)] border border-[var(--color-border-color)] rounded-full text-[10px] font-black text-[var(--color-text-muted)] uppercase">{{ $item->guard_name }}</span></td>
                                <td class="p-4 text-center font-bold text-red-500/80 text-sm">{{ $item->deleted_at->format('d M, Y h:i A') }}</td>
                                <td class="p-4 flex justify-end gap-2">
                                    <button wire:click="restore({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-green-500/10 text-green-600 hover:bg-green-500 hover:text-white dark:bg-[var(--color-background)] dark:border-[var(--color-border-color)] dark:hover:bg-green-500 dark:hover:border-transparent" title="Restore">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    </button>
                                    <button wire:click="confirmForceDelete({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-red-50 text-red-500 hover:bg-red-600 hover:text-white dark:bg-[var(--color-background)] dark:border-[var(--color-border-color)] dark:hover:bg-red-600 dark:hover:border-transparent" title="Force Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-16 text-center text-[var(--color-text-muted)] font-black uppercase text-xs tracking-widest italic opacity-50">{{ __('messages.trash_is_empty') ?? 'Trash is empty' }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 md:p-5 bg-[var(--color-background)]/30 border-t border-[var(--color-border-color)]">
                {{ $permissions->links('livewire.parts.pagination') }}
            </div>
        </div>

        {{-- ផ្នែក Mobile View (Card) --}}
        <div class="md:hidden space-y-3">
            <div class="flex items-center gap-3 px-2 mb-1">
                <input type="checkbox" wire:model.live="selectAll" id="selectAllMobileTrash" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/30 bg-transparent cursor-pointer">
                <label for="selectAllMobileTrash" class="text-sm font-black text-[var(--color-text-main)]">{{ __('messages.select_all') ?? 'Select All' }}</label>
            </div>

            @forelse($permissions as $item)
                <div x-data @click="if(!$event.target.closest('button') && $event.target.tagName !== 'INPUT') $el.querySelector('input[type=checkbox]').click()" 
                    class="bg-[var(--color-card-bg)] p-4 rounded-xl shadow-sm border border-[var(--color-border-color)] flex flex-col gap-3 cursor-pointer hover:bg-[var(--color-background)]/50 transition-all select-none">
                    
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <input type="checkbox" wire:model.live="selectedPermissions" value="{{ $item->id }}" class="w-4 h-4 mt-1 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/30 bg-transparent cursor-pointer flex-shrink-0">
                            <div class="min-w-0">
                                <h4 class="font-black text-[var(--color-text-main)] text-base truncate line-through opacity-70">{{ $item->name }}</h4>
                                <span class="inline-block mt-1 px-2 py-0.5 bg-[var(--color-background)] border border-[var(--color-text-main)] rounded-md text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ $item->guard_name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-dashed border-[var(--color-border-color)] w-full"></div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.deleted_at') ?? 'Deleted At' }}</span>
                            <span class="text-[11px] font-bold text-red-500/80">{{ $item->deleted_at->format('d M, Y h:i A') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="restore({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-green-500/10 text-green-600 hover:bg-green-500 hover:text-white flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                            <button wire:click="confirmForceDelete({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-red-50 text-red-500 hover:bg-red-600 hover:text-white flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-[var(--color-card-bg)] p-8 rounded-xl border border-[var(--color-border-color)] text-center text-[var(--color-text-muted)] font-black uppercase tracking-widest text-xs italic">{{ __('messages.trash_is_empty') ?? 'Trash is empty' }}</div>
            @endforelse

            <div class="w-full flex justify-center mt-4">
                {{ $permissions->links('livewire.parts.pagination') }}
            </div>
        </div>
    </div>

    {{-- ការហៅ Component Reusable Modal Delete មកប្រើ --}}
    <x-modals.delete 
        :isOpen="$isDeleteModalOpen" 
        onClose="$set('isDeleteModalOpen', false)" 
        onConfirm="executeForceDelete" 
        message="{!! $deleteId ? (__('messages.force_delete_warning') ?? 'Are you sure you want to permanently delete this data?') : (__('messages.bulk_force_delete_warning') ?? 'Are you sure you want to permanently delete the selected data?') !!}"
    />
</div>