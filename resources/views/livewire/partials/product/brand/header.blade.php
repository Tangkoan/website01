<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl md:text-3xl font-black text-[var(--color-text-main)] tracking-tight flex items-center gap-3">
            <span class="p-2 bg-[var(--color-primary)]/10 rounded-lg md:rounded-xl text-[var(--color-primary)] text-xl md:text-2xl">📦</span>
            {{ __('messages.brand_management') ?? 'Brands Management' }}
        </h2>
    </div>
    
    <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0 relative flex-wrap">
        <x-auth-link permission="view-brand-logs" href="{{ route('settings.logs', 'brand') }}" wire:navigate class="p-2 md:p-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-blue-500 hover:bg-blue-500 hover:text-white transition-all flex items-center gap-2" title="{{ __('messages.logs') ?? 'Logs' }}">
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="hidden md:inline-block text-sm font-black">{{ __('messages.logs') ?? 'Logs' }}</span>
        </x-auth-link>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.outside="open = false" type="button" class="p-2 md:p-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-[var(--color-text-main)] hover:brightness-95 transition-all flex items-center gap-2" title="{{ __('messages.columns') ?? 'Columns' }}">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
            </button>
            <div x-show="open" class="absolute right-0 mt-2 w-48 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-xl shadow-lg z-[100] p-2 hidden" :class="{'hidden': !open}">
                <div class="space-y-1">
                    @foreach($availableColumns ?? [] as $key => $label)
                        <label class="flex items-center gap-3 px-2 py-2 hover:bg-[var(--color-background)] rounded-lg cursor-pointer">
                            <input type="checkbox" wire:model.live="selectedColumns" value="{{ $key }}" class="w-4 h-4 rounded text-[var(--color-primary)]">
                            <span class="text-xs font-bold text-[var(--color-text-main)]">{{ __('messages.' . $key) ?? $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <button wire:click="reloadData" class="p-2 md:p-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-[var(--color-text-main)] hover:brightness-95 transition-all" title="{{ __('messages.reload') ?? 'Reload' }}">
            <svg wire:loading.class="animate-spin text-[var(--color-primary)]" wire:target="reloadData" class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        </button>

        <x-auth-link permission="view-brand-trash" href="{{ route('settings.trash', 'brand') }}" wire:navigate class="px-3 py-2 md:px-4 md:py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center gap-2">
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            <span class="hidden md:inline-block text-sm font-black">{{ __('messages.trash') ?? 'Trash' }}</span>
        </x-auth-link>

        <x-auth-button permission="create-brand" wire:click="openModal" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 md:px-5 py-2 md:py-2.5 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-black rounded-lg shadow-lg hover:brightness-110 active:scale-95 transition-all text-sm">
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span class="hidden md:block">{{ __('messages.add_new') ?? 'Add New' }}</span>
        </x-auth-button>
    </div>
</div>