<div class="bg-[var(--color-card-bg)] p-3 rounded-xl border border-[var(--color-border-color)] shadow-sm flex flex-col lg:flex-row items-center justify-between gap-4 mb-4">
    <div class="relative w-full lg:w-96 group">
        <!-- Search Icon -->
        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[var(--color-text-muted)] group-focus-within:text-[var(--color-primary)] pointer-events-none">
            <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </span>
        
        <!-- Input Field (បានដូរ pr-4 ទៅ pr-10 ដើម្បីទុកចន្លោះឲ្យប៊ូតុង X) -->
        <input type="text" wire:model.live.debounce.300ms="searchTerm" class="w-full h-10 md:h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] focus:border-[var(--color-primary)] text-[var(--color-text-main)] rounded-lg pl-10 pr-10 text-sm font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 outline-none transition-all" placeholder="{{ __('messages.search') ?? 'Search...' }}">
        
        <!-- Clear Button (បង្ហាញតែពេលមានការវាយអក្សរប៉ុណ្ណោះ) -->
        @if(!empty($searchTerm))
            <button wire:click="$set('searchTerm', '')" type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[var(--color-text-muted)] hover:text-red-500 transition-colors" title="{{ __('messages.clear') ?? 'Clear' }}">
                <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        @endif
    </div>

    <div x-data="{ hasSelection: @entangle('selectedItems').live }" x-show="hasSelection.length > 0" x-transition:enter="transition ease-out duration-200" style="display: none;" class="flex items-center gap-2 p-1.5 bg-[var(--color-primary)]/10 rounded-lg border border-[var(--color-primary)]/20 w-full lg:w-auto">
        <span class="text-xs font-black text-[var(--color-primary)] px-3"><span x-text="hasSelection.length"></span> {{ __('messages.selected') ?? 'Selected' }}</span>
        <div class="flex gap-2 w-full sm:w-auto">
            <x-auth-button permission="edit-category" wire:click="bulkEdit" class="flex-1 sm:flex-none px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-black rounded-md transition-colors shadow-sm">{{ __('messages.bulk_edit') ?? 'Bulk Edit' }}</x-auth-button>
            <x-auth-button permission="delete-category" wire:click="confirmDelete()" class="flex-1 sm:flex-none px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-black rounded-md transition-colors shadow-sm">{{ __('messages.bulk_delete') ?? 'Bulk Delete' }}</x-auth-button>
        </div>
    </div>
</div>