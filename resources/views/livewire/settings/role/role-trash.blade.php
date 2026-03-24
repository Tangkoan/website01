<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    
    {{-- ១. Header (Title & Back Button) --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-[var(--color-text-main)] tracking-tight flex items-center gap-3">
                <span class="p-2.5 bg-red-500/10 rounded-lg md:rounded-xl text-red-500 text-xl md:text-2xl">
                    🗑️
                </span>
                {{ __('messages.roles_trash') ?? 'Roles Trash' }}
            </h2>
        </div>
        <div class="flex items-center gap-2 w-full md:w-auto">
            <a href="{{ route('settings.roles') }}" wire:navigate class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 md:py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-sm font-black text-[var(--color-text-main)] hover:brightness-95 transition-all">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('messages.back') ?? 'Back' }}
            </a>
        </div>
    </div>

    {{-- ២. Filters (Search & Bulk Action) --}}
    <div class="bg-[var(--color-card-bg)] p-3 rounded-xl border border-[var(--color-border-color)] shadow-sm flex flex-col lg:flex-row items-center justify-between gap-4">
        <div class="relative w-full lg:w-96 group">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[var(--color-text-muted)] group-focus-within:text-[var(--color-primary)]">
                <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                class="w-full h-10 md:h-11 bg-[var(--color-background)] border-transparent text-[var(--color-text-main)] rounded-lg pl-10 pr-4 text-sm font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 outline-none" 
                placeholder="{{ __('messages.search_deleted_data') ?? 'Search deleted data...' }}">
        </div>

        @if(count($selectedRoles) > 0)
            <div class="flex flex-col sm:flex-row items-center gap-2 p-1.5 bg-[var(--color-primary)]/10 rounded-lg border border-[var(--color-primary)]/20 w-full lg:w-auto">
                <span class="text-xs font-black text-[var(--color-primary)] px-3">{{ count($selectedRoles) }} {{ __('messages.selected') }}</span>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button wire:click="restore()" class="flex-1 sm:flex-none px-3 py-2 bg-green-500 text-white text-xs font-black rounded-md">{{ __('messages.bulk_restore') ?? 'Bulk Restore' }}</button>
                    <button wire:click="confirmForceDelete()" class="flex-1 sm:flex-none px-3 py-2 bg-red-500 text-white text-xs font-black rounded-md">{{ __('messages.bulk_delete_forever') ?? 'Bulk Delete Forever' }}</button>
                </div>
            </div>
        @endif
    </div>

    <div class="space-y-4">
        {{-- ៣. Desktop Table (បង្ហាញតែលើ Screen ធំ) --}}
        <div class="hidden md:block bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm min-h-[300px] relative overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[var(--color-background)]/50 border-b border-[var(--color-border-color)]">
                        <th class="p-4 w-16 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/30 bg-transparent dark:bg-[var(--color-background)] checked:bg-[var(--color-primary)] checked:border-[var(--color-primary)] transition-all cursor-pointer">
                        </th>
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.role_name') ?? 'Role Name' }}</th>
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center">{{ __('messages.guard') ?? 'Guard' }}</th>
                        <th class="p-4 text-[10px] font-black text-red-500 uppercase tracking-widest">{{ __('messages.deleted_at') ?? 'Deleted At' }}</th>
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-right">{{ __('messages.actions') ?? 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border-color)] relative">
                    @forelse($roles as $item)
                        <tr wire:key="desktop-row-{{ $item->id }}" class="hover:bg-[var(--color-background)]/50 transition-all cursor-pointer select-none">
                            <td class="p-4 text-center">
                                <input type="checkbox" wire:model.live="selectedRoles" value="{{ $item->id }}" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/30 bg-transparent dark:bg-[var(--color-background)] checked:bg-[var(--color-primary)] checked:border-[var(--color-primary)] transition-all cursor-pointer">
                            </td>
                            <td class="p-4">
                                <div class="font-black text-[var(--color-text-main)] transition-colors text-sm md:text-base">{{ $item->name }}</div>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 bg-[var(--color-background)] border border-[var(--color-border-color)] rounded-full text-[10px] font-black text-[var(--color-text-muted)] uppercase">{{ $item->guard_name }}</span>
                            </td>
                            <td class="p-4 text-red-500 font-bold text-sm">
                                {{ $item->deleted_at->format('d M, Y H:i A') }}
                            </td>
                            <td class="p-4 flex justify-end gap-2">
                                <button wire:click="restore({{ $item->id }})" title="{{ __('messages.restore') }}" class="p-2 rounded-lg transition-all border border-transparent bg-green-50 text-green-600 hover:bg-green-600 hover:text-white dark:bg-[var(--color-background)] dark:border-[var(--color-border-color)] dark:hover:bg-green-600 dark:hover:border-transparent">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                </button>
                                <button wire:click="confirmForceDelete({{ $item->id }})" title="{{ __('messages.delete_forever') }}" class="p-2 rounded-lg transition-all border border-transparent bg-red-50 text-red-600 hover:bg-red-600 hover:text-white dark:bg-[var(--color-background)] dark:border-[var(--color-border-color)] dark:hover:bg-red-600 dark:hover:border-transparent">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-16 text-center text-[var(--color-text-muted)] uppercase text-xs italic opacity-50">{{ __('messages.no_data_deleted') ?? 'No deleted data' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ៤. Mobile Cards (បង្ហាញតែលើ Screen តូច) --}}
        <div class="md:hidden space-y-3">
            <div class="flex items-center gap-3 px-2 mb-1">
                <input type="checkbox" wire:model.live="selectAll" id="selectAllMobile" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] bg-transparent cursor-pointer">
                <label for="selectAllMobile" class="text-sm font-black text-[var(--color-text-main)]">{{ __('messages.select_all') ?? 'Select All' }}</label>
            </div>

            @forelse($roles as $item)
                <div wire:key="mobile-row-{{ $item->id }}" class="bg-[var(--color-card-bg)] p-4 rounded-xl shadow-sm border border-[var(--color-border-color)] flex flex-col gap-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <input type="checkbox" wire:model.live="selectedRoles" value="{{ $item->id }}" class="w-4 h-4 mt-1 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] bg-transparent cursor-pointer flex-shrink-0">
                            <div class="min-w-0">
                                <h4 class="font-black text-[var(--color-text-main)] text-base truncate">{{ $item->name }}</h4>
                                <span class="inline-block mt-1 px-2 py-0.5 bg-[var(--color-background)] border border-[var(--color-border-color)] rounded-md text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ $item->guard_name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-dashed border-[var(--color-border-color)] w-full"></div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-red-500 uppercase tracking-widest">Deleted At</span>
                            <span class="text-[10px] font-bold text-[var(--color-text-muted)] uppercase tracking-widest">{{ $item->deleted_at->format('d M, Y') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="restore({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-green-50 text-green-600 hover:bg-green-600 hover:text-white flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            </button>
                            <button wire:click="confirmForceDelete({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-[var(--color-card-bg)] p-8 rounded-xl border border-[var(--color-border-color)] text-center text-[var(--color-text-muted)] font-black uppercase tracking-widest text-xs italic opacity-50">{{ __('messages.no_data') ?? 'No Data' }}</div>
            @endforelse
        </div>
    </div>
    
    {{-- ៥. Pagination & Per Page selection --}}
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 p-4 bg-[var(--color-background)] border border-[var(--color-border-color)] rounded-xl mt-4">
        
        <div class="w-full sm:w-auto flex justify-center">
            {{ $roles->links('livewire.parts.pagination') }}
        </div>
    </div>

    {{-- ៦. Delete Modal --}}
    <x-modals.delete 
        :isOpen="$isDeleteModalOpen" 
        onClose="$set('isDeleteModalOpen', false)" 
        onConfirm="executeDelete" 
        message="This will permanently remove the selected role! This cannot be undone."
    />
</div>