<div class="bg-[var(--color-card-bg)] p-3 rounded-xl border border-[var(--color-border-color)] shadow-sm flex flex-col lg:flex-row items-center justify-between gap-4">
    <div class="relative w-full lg:w-96 group">
        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[var(--color-text-muted)] group-focus-within:text-[var(--color-primary)]">
            <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </span>
        <input type="text" wire:model.live.debounce.300ms="searchTerm" 
            class="w-full h-10 md:h-11 bg-[var(--color-background)] border-transparent text-[var(--color-text-main)] rounded-lg pl-10 pr-4 text-sm font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 outline-none" 
            placeholder="{{ __('messages.search') ?? 'Search' }}...">
    </div>

    @if(count($selectedPermissions) > 0)
        <div class="flex flex-col sm:flex-row items-center gap-2 p-1.5 bg-[var(--color-primary)]/10 rounded-lg border border-[var(--color-primary)]/20 w-full lg:w-auto">
            <span class="text-xs font-black text-[var(--color-primary)] px-3">{{ count($selectedPermissions) }} {{ __('messages.selected') ?? 'Selected' }}</span>
            <div class="flex gap-2 w-full sm:w-auto">
                {{-- ប្រើ x-auth-button ជំនួស @can --}}
                <x-auth-button permission="bulk-edit-permission" wire:click="bulkEdit" class="flex-1 sm:flex-none px-3 py-2 bg-amber-500 text-white text-xs font-black rounded-md">{{ __('messages.bulk_edit') ?? 'Bulk Edit' }}</x-auth-button>
                
                <x-auth-button permission="bulk-delete-permission" wire:click="confirmDelete()" class="flex-1 sm:flex-none px-3 py-2 bg-red-500 text-white text-xs font-black rounded-md">{{ __('messages.bulk_delete') ?? 'Bulk Delete' }}</x-auth-button>
            </div>
        </div>
    @endif
</div>