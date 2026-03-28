<div class="hidden md:block bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm min-h-[300px] relative">
    <div wire:loading wire:target="getUsersProperty, reloadData" class="absolute inset-0 z-10 bg-[var(--color-card-bg)]/50 backdrop-blur-sm flex items-center justify-center rounded-xl">
        <svg class="animate-spin w-8 h-8 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-[var(--color-background)]/50 border-b border-[var(--color-border-color)]">
                    @canany(['bulk-edit-permission', 'bulk-delete-permission'])
                        <th class="p-4 w-16 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/30 bg-transparent dark:bg-[var(--color-background)] checked:bg-[var(--color-primary)] checked:border-[var(--color-primary)] transition-all cursor-pointer">
                        </th>
                    @endcanany
                    
                    @if(in_array('name', $selectedColumns))
                        <th wire:click="sortBy('name')" class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest cursor-pointer hover:text-[var(--color-primary)] transition-colors">
                            <div class="flex items-center gap-2">
                                {{ __('messages.name') ?? 'Name' }} 
                                @if($sortField === 'name') 
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg> 
                                @endif
                            </div>
                        </th>
                    @endif

                    @if(in_array('guard_name', $selectedColumns))
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center">{{ __('messages.guard') ?? 'Guard' }}</th>
                    @endif

                    <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-right">{{ __('messages.actions') ?? 'Actions' }}</th>
                </tr>
            </thead>

            {{-- ចំណុចសំខាន់៖ បញ្ចូល Alpine.js States សម្រាប់ Drag-to-Select ក្នុង tbody --}}
            <tbody x-data="{ isMouseDown: false, checkStatus: true }" 
                   @mousedown="isMouseDown = true" 
                   @mouseup.window="isMouseDown = false"
                   class="divide-y divide-[var(--color-border-color)] relative">

                @forelse($permissions as $item)
                    <tr wire:key="desktop-row-{{ $item->id }}" 
                        {{-- logic សម្រាប់ចុចដំបូង --}}
                        @mousedown="
                            if(!$event.target.closest('button')) {
                                let cb = $el.querySelector('input[type=checkbox]');
                                if(cb) {
                                    if($event.target.tagName !== 'INPUT') {
                                        checkStatus = !cb.checked;
                                        cb.checked = checkStatus;
                                        cb.dispatchEvent(new Event('change'));
                                    } else {
                                        checkStatus = cb.checked;
                                    }
                                }
                            }
                        "
                        {{-- logic សម្រាប់អូស Mouse កាត់ --}}
                        @mouseenter="
                            if(isMouseDown && !$event.target.closest('button')) {
                                let cb = $el.querySelector('input[type=checkbox]');
                                if(cb && cb.checked !== checkStatus) {
                                    cb.checked = checkStatus;
                                    cb.dispatchEvent(new Event('change'));
                                }
                            }
                        "
                        class="hover:bg-[var(--color-background)]/50 transition-all group cursor-pointer select-none">
                        
                        @canany(['bulk-edit-permission', 'bulk-delete-permission'])
                            <td class="p-4 text-center">
                                <input type="checkbox" wire:model.live="selectedPermissions" value="{{ $item->id }}" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/30 bg-transparent dark:bg-[var(--color-background)] checked:bg-[var(--color-primary)] checked:border-[var(--color-primary)] transition-all cursor-pointer">
                            </td>
                        @endcanany
                        
                        @if(in_array('name', $selectedColumns))
                            <td class="p-4">
                                <div class="font-black text-[var(--color-text-main)] group-hover:text-[var(--color-primary)] transition-colors text-sm md:text-base">{{ $item->name }}</div>
                            </td>
                        @endif

                        @if(in_array('guard_name', $selectedColumns))
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 bg-[var(--color-background)] border border-[var(--color-border-color)] rounded-full text-[10px] font-black text-[var(--color-text-muted)] uppercase">{{ $item->guard_name }}</span>
                            </td>
                        @endif

                        <td class="p-4 flex justify-end gap-2">
                            @can('edit-permission')
                                <button wire:click="editPermission({{ $item->id }})" 
                                    class="p-2 rounded-lg transition-all border border-transparent bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-[var(--color-primary-text)] dark:bg-[var(--color-background)] dark:border-[var(--color-border-color)] dark:hover:bg-[var(--color-primary)] dark:hover:border-transparent">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                            @endcan
                            
                            @can('delete-permission')
                                <button wire:click="confirmDelete({{ $item->id }})" 
                                    class="p-2 rounded-lg transition-all border border-transparent bg-red-50 text-red-500 hover:bg-red-500 hover:text-white dark:bg-[var(--color-background)] dark:border-[var(--color-border-color)] dark:hover:bg-red-500 dark:hover:border-transparent">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-16 text-center text-[var(--color-text-muted)] uppercase text-xs italic opacity-50">{{ __('messages.no_data') ?? 'No Data' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 md:p-5 bg-[var(--color-background)]/30 border-t border-[var(--color-border-color)]">
        {{ $permissions->links('livewire.parts.pagination') }}
    </div>
</div>