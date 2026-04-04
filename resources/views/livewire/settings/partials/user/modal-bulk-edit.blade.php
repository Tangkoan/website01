@if($isBulkEditModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 dark:bg-black/60 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-4xl rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden flex flex-col md:flex-row max-h-[90vh]">
            <div class="w-full md:w-1/3 bg-[var(--color-background)] border-r border-[var(--color-border-color)] p-5 overflow-y-auto max-h-[35vh] md:max-h-full">
                <h3 class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest mb-3">{{ __('messages.editing') ?? 'Editing' }} {{ count($selectedItemsQueue) }} {{ __('messages.items') ?? 'Items' }}</h3>
                <div class="space-y-2">
                    @foreach($selectedItemsQueue as $index => $userId)
                        @php
                            // ✅ កែប្រែ៖ ទាញឈ្មោះ User មកបង្ហាញ ដោយសារឥឡូវ Queue យើងផ្ទុកតែ ID ប៉ុណ្ណោះ
                            $userName = \App\Models\User::find($userId)?->name ?? 'Unknown';
                        @endphp
                        <button wire:key="bulk-item-{{ $index }}" wire:click="jumpToBulkItem({{ $index }})" 
                            class="w-full text-left p-2.5 md:p-3 rounded-lg font-bold text-xs md:text-sm transition-all flex items-center justify-between
                            {{ $currentBulkIndex === $index ? 'bg-[var(--color-primary)] text-[var(--color-primary-text)] shadow-md' : 'bg-[var(--color-card-bg)] text-[var(--color-text-main)] border border-[var(--color-border-color)]' }}">
                            <span class="truncate pr-2">{{ $userName }}</span>
                            @if($currentBulkIndex === $index) <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"></path></svg> @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="w-full md:w-2/3 p-5 md:p-6 flex flex-col justify-center bg-[var(--color-card-bg)] overflow-y-auto">
                <div class="mb-5 md:mb-6 flex justify-between items-center">
                    <h3 class="text-lg md:text-xl font-black text-[var(--color-text-main)]">{{ __('messages.edit') ?? 'Edit' }} <span class="text-[var(--color-primary)]">({{ $currentBulkIndex + 1 }}/{{ count($selectedItemsQueue) }})</span></h3>
                    <button wire:click="closeBulkEdit" class="text-[var(--color-text-muted)] hover:text-red-500 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.name') ?? 'Name' }}</label>
                            <input type="text" wire:model="bulkItemName" class="w-full h-10 md:h-11 bg-[var(--color-background)] border border-transparent focus:border-[var(--color-primary)]/50 text-[var(--color-text-main)] rounded-lg px-3 md:px-4 font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 outline-none text-sm">
                            @error('bulkItemName') <span class="text-red-500 text-[10px] font-bold italic">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.email') ?? 'Email' }}</label>
                            <input type="email" wire:model="bulkItemEmail" class="w-full h-10 md:h-11 bg-[var(--color-background)] border border-transparent focus:border-[var(--color-primary)]/50 text-[var(--color-text-main)] rounded-lg px-3 md:px-4 font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 outline-none text-sm">
                            @error('bulkItemEmail') <span class="text-red-500 text-[10px] font-bold italic">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.role') ?? 'Role' }}</label>
                        <select wire:model="bulkItemRole" class="w-full h-10 md:h-11 bg-[var(--color-background)] border border-transparent focus:border-[var(--color-primary)]/50 text-[var(--color-text-main)] rounded-lg px-3 md:px-4 font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 transition-all outline-none text-sm">
                            <option value="">{{ __('messages.select_role') ?? 'Select Role' }}</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        @error('bulkItemRole') <span class="text-red-500 text-[10px] font-bold italic">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2 flex items-center gap-3 bg-[var(--color-background)]/50 p-3 rounded-xl border border-[var(--color-border-color)] transition-colors mt-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="bulkItemStatus" class="sr-only peer">
                            <div class="w-9 h-5 bg-[var(--color-border-color)] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[var(--color-border-color)] after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--color-primary)]"></div>
                        </label>
                        <span class="text-sm font-bold text-[var(--color-text-main)] uppercase">{{ $bulkItemStatus ? __('messages.account_active') : __('messages.account_inactive') }}</span>
                    </div>
                </div>

                <div class="mt-6 md:mt-8 flex gap-3">
                    <button wire:click="skipBulkItem" class="flex-1 h-10 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-black rounded-lg hover:brightness-95 transition-all uppercase tracking-widest text-[10px]">{{ __('messages.skip') ?? 'Skip' }}</button>
                    <button wire:click="saveAndNextBulkItem" class="flex-[2] h-10 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-black rounded-lg shadow-xl hover:brightness-110 active:scale-95 transition-all uppercase tracking-widest text-[10px]">{{ __('messages.save_next') ?? 'Save & Next' }}</button>
                </div>
            </div>
        </div>
    </div>
@endif