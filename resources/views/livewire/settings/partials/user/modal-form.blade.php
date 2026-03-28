@if($isModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 dark:bg-black/60 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-2xl rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden transition-colors">
            <div class="p-5 border-b border-[var(--color-border-color)] flex justify-between items-center bg-[var(--color-background)]/50 transition-colors">
                <h3 class="text-lg font-bold text-[var(--color-text-main)]">{{ $userId ? __('messages.edit_data') : __('messages.create_new_account') }}</h3>
                <button wire:click="$set('isModalOpen', false)" class="text-[var(--color-text-muted)] hover:text-red-500 font-bold text-2xl transition-colors">&times;</button>
            </div>
            
            <form wire:submit.prevent="saveUser" class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.full_name') }}</label>
                        <input type="text" wire:model="name" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors">
                        @error('name') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.email') }}</label>
                        <input type="email" wire:model="email" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors">
                        @error('email') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">{{ __('messages.password') }} {{ $userId ? __('messages.leave_blank_if_no_change') : '' }}</label>
                        <input type="password" wire:model="password" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg px-4 text-sm focus:ring-2 focus:ring-[var(--color-primary)] outline-none shadow-sm transition-colors">
                        @error('password') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider mb-2">{{ __('messages.role') }}</label>
                        <div class="grid grid-cols-2 gap-2 bg-[var(--color-background)]/50 p-4 rounded-xl border border-[var(--color-border-color)] transition-colors">
                            @foreach($roles as $r)
                                <label class="flex items-center space-x-3 cursor-pointer p-2 rounded-lg transition-all border {{ $role_id == $r->id ? 'border-[var(--color-primary)] bg-[var(--color-primary)]/10' : 'border-transparent hover:border-[var(--color-border-color)] hover:bg-[var(--color-card-bg)]' }} shadow-sm">
                                    <input type="radio" wire:model="role_id" value="{{ $r->id }}" name="role_selection" class="h-4 w-4 text-[var(--color-primary)] border-[var(--color-border-color)] bg-[var(--color-card-bg)] focus:ring-[var(--color-primary)]">
                                    <span class="text-sm text-[var(--color-text-main)] font-medium">{{ $r->name }} <span class="text-[10px] text-[var(--color-text-muted)] bg-[var(--color-background)] px-1 py-0.5 rounded ml-1 border border-[var(--color-border-color)]">L:{{ $r->level }}</span></span>
                                </label>
                            @endforeach
                        </div>
                        @error('role_id') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2 md:col-span-2 flex items-center gap-3 bg-[var(--color-background)]/50 p-3 rounded-xl border border-[var(--color-border-color)] transition-colors">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="status" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[var(--color-primary)]"></div>
                        </label>
                        <span class="text-sm font-bold text-[var(--color-text-main)] uppercase">{{ $status ? __('messages.account_active') : __('messages.account_inactive') }}</span>
                    </div>

                </div>

                <div class="pt-6 flex justify-end gap-3 border-t border-[var(--color-border-color)] mt-6 transition-colors">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-5 py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-bold rounded-lg text-sm hover:brightness-95 transition-all shadow-sm">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="px-5 py-2.5 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-bold rounded-lg shadow-md text-sm hover:brightness-110 transition-all flex items-center">
                        <span wire:loading.remove>{{ __('messages.save_data') }}</span>
                        <span wire:loading>{{ __('messages.processing') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif