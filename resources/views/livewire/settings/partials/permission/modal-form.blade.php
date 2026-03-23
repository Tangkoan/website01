@if($isModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 dark:bg-black/60 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-md rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden">
            <div class="p-5 md:p-6 border-b border-[var(--color-border-color)] flex justify-between bg-[var(--color-card-bg)]">
                <h3 class="text-lg md:text-xl font-black text-[var(--color-text-main)]">{{ $permissionId ? (__('messages.edit') ?? 'Edit') : (__('messages.add_new') ?? 'Add New') }}</h3>
            </div>
            <form wire:submit.prevent="savePermission" class="p-5 md:p-6 space-y-4 md:space-y-5 bg-[var(--color-card-bg)]">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.name') ?? 'Name' }}</label>
                    <input type="text" wire:model="name" class="w-full h-10 md:h-11 bg-[var(--color-background)] border border-transparent focus:border-[var(--color-primary)]/50 text-[var(--color-text-main)] rounded-lg px-3 md:px-4 font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 transition-all outline-none text-sm">
                    @error('name') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.guard') ?? 'Guard' }}</label>
                    <select wire:model="guard_name" class="w-full h-10 md:h-11 bg-[var(--color-background)] border border-transparent focus:border-[var(--color-primary)]/50 text-[var(--color-text-main)] rounded-lg px-3 md:px-4 font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 transition-all outline-none text-sm">
                        <option value="web">web</option><option value="api">api</option>
                    </select>
                </div>
                <div class="pt-2 flex gap-3">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="flex-1 h-10 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-black rounded-lg hover:brightness-95 dark:hover:brightness-110 transition-all uppercase tracking-widest text-[10px] shadow-sm">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                    <button type="submit" class="flex-[2] h-10 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-black rounded-lg shadow-xl shadow-[var(--color-primary)]/20 hover:brightness-110 active:scale-95 transition-all uppercase tracking-widest text-[10px]">{{ __('messages.save') ?? 'Save' }}</button>
                </div>
            </form>
        </div>
    </div>
@endif