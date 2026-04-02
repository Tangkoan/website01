<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-2xl md:text-3xl font-black text-[var(--color-text-main)] tracking-tight flex items-center gap-3">
            <span class="p-2 bg-[var(--color-primary)]/10 rounded-lg md:rounded-xl text-[var(--color-primary)] text-xl md:text-2xl">🔐</span>
            {{ __('messages.permissions') ?? 'Permissions' }}
        </h2>
    </div>
    <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0 relative">
        
        {{-- Column Picker --}}
        <div x-data="{ showColumns: false }" class="relative">
            <button @click="showColumns = !showColumns" @click.outside="showColumns = false" type="button" class="p-2 md:p-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-[var(--color-text-main)] hover:brightness-95 transition-all flex-shrink-0 flex items-center gap-2">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
            </button>
            <div x-show="showColumns" style="display: none;" x-transition class="absolute left-0 mt-2 w-48 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-xl shadow-2xl p-3 z-[100]">
                <h4 class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest mb-2 border-b border-[var(--color-border-color)] pb-2">Show Columns</h4>
                <div class="space-y-2">
                    @foreach($availableColumns as $key => $label)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" wire:model.live="selectedColumns" value="{{ $key }}" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] checked:bg-[var(--color-primary)] transition-all">
                            <span class="text-sm font-bold text-[var(--color-text-main)] group-hover:text-[var(--color-primary)]">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ប៊ូតុង Activity Log (ប្រើ x-auth-link) --}}
        <x-auth-link permission="view-permission-logs" href="{{ route('settings.logs', 'permission') }}" wire:navigate class="p-2 md:p-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-blue-500 hover:bg-blue-500 hover:text-white transition-all flex-shrink-0 flex items-center gap-2" title="Activity Logs / កំណត់ត្រាសកម្មភាព">
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="hidden md:inline-block text-sm font-black">Logs</span>
        </x-auth-link>

        {{-- ប៊ូតុង Reload --}}
        <button wire:click="reloadData" class="p-2 md:p-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-[var(--color-text-main)] hover:brightness-95 transition-all">
            <svg wire:loading.class="animate-spin text-[var(--color-primary)]" wire:target="reloadData" class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        </button>

        {{-- ប៊ូតុង Trash (ប្រើ x-auth-link) --}}
        <x-auth-link permission="view-permission-trash" href="{{ route('settings.trash', 'permission') }}" wire:navigate class="px-3 py-2 md:px-4 md:py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-red-500 hover:bg-red-500 hover:text-white transition-all flex-shrink-0 flex items-center gap-2" title="Trash / ធុងសំរាម">
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            <span class="hidden md:inline-block text-sm font-black">{{ __('messages.permissions_trash') ?? 'ធុងសំរាម' }}</span>
        </x-auth-link>
        
        {{-- ប៊ូតុង Add New (ប្រើ x-auth-button) --}}
        <x-auth-button permission="create-permission" wire:click="openModal" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 md:px-5 py-2 md:py-2.5 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-black rounded-lg shadow-lg hover:brightness-110 active:scale-95 transition-all text-sm">
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span class="hidden md:block">
                {{ __('messages.add_new') ?? 'Add New' }}
            </span>
        </x-auth-button>
    </div>
</div>