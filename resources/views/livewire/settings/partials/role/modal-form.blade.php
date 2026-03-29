@if($isModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-2xl rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden">
            <div class="p-5 border-b border-[var(--color-border-color)]">
                <h3 class="text-xl font-black text-[var(--color-text-main)]">{{ $roleId ? 'Edit Role' : 'Add New Role' }}</h3>
            </div>
            
            <form wire:submit.prevent="saveRole" class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4"> 
                    {{-- Role Name --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.role_name') ?? 'Role Name' }}</label>
                        <input type="text" wire:model="name" class="w-full h-11 bg-[var(--color-background)] border-transparent rounded-lg px-4 font-bold text-[var(--color-text-main)] outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                        @error('name') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    {{-- Level (បន្ថែមថ្មីជាមួយ Max limit) --}}
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.level') ?? 'Level' }}</label>
                            <span class="text-[9px] text-[var(--color-text-muted)]">Max: {{ $maxAllowedLevel ?? 'N/A' }}</span>
                        </div>
                        <input type="number" wire:model="level" min="1" max="{{ $maxAllowedLevel ?? 100 }}" class="w-full h-11 bg-[var(--color-background)] border-transparent rounded-lg px-4 font-bold text-[var(--color-text-main)] outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                        @error('level') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    {{-- Guard --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">Guard</label>
                        <select wire:model="guard_name" class="w-full h-11 bg-[var(--color-background)] border-transparent rounded-lg px-4 font-bold text-[var(--color-text-main)] outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                            <option value="web">web</option>
                            <option value="api">api</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="flex-1 h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-black rounded-lg uppercase tracking-widest text-[10px] hover:brightness-95 transition-all">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                    <button type="submit" class="flex-[2] h-11 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-black rounded-lg shadow-xl uppercase tracking-widest text-[10px] hover:brightness-110 transition-all">Save Role</button>
                </div>
            </form>
        </div>
    </div>
@endif