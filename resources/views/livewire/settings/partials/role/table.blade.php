<div class="hidden md:block bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm min-h-[300px] relative">
    <div wire:loading wire:target="getUsersProperty, reloadData" class="absolute inset-0 z-10 bg-[var(--color-card-bg)]/50 backdrop-blur-sm flex items-center justify-center rounded-xl">
        <svg class="animate-spin w-8 h-8 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-[var(--color-background)]/50 border-b border-[var(--color-border-color)]">
                    <th class="p-4 w-16 text-center">
                        <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] bg-transparent checked:bg-[var(--color-primary)] cursor-pointer">
                    </th>
                    @if(in_array('name', $selectedColumns))
                        <th wire:click="sortBy('name')" class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest cursor-pointer hover:text-[var(--color-primary)] transition-colors">
                            <div class="flex items-center gap-2">
                                {{ __('messages.role_name') ?? 'Role Name' }} 
                                @if($sortField === 'name') <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg> @endif
                            </div>
                        </th>
                    @endif
                    
                    {{-- Column Level (បន្ថែមថ្មី) --}}
                    @if(in_array('level', $selectedColumns))
                        <th wire:click="sortBy('level')" class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest cursor-pointer hover:text-[var(--color-primary)] transition-colors text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{ __('messages.level') ?? 'Level' }}
                                @if($sortField === 'level') <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg> @endif
                            </div>
                        </th>
                    @endif

                    @if(in_array('guard_name', $selectedColumns))
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center">Guard</th>
                    @endif
                    
                    @if(in_array('created_at', $selectedColumns))
                        <th wire:click="sortBy('created_at')" class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest cursor-pointer hover:text-[var(--color-primary)] transition-colors">
                            <div class="flex items-center gap-2">
                                {{ __('messages.created_date') ?? 'Created Date' }} 
                                @if($sortField === 'created_at') <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg> @endif
                            </div>
                        </th>
                    @endif

                    <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center">{{ __('messages.actions') ?? 'Actions' }}</th>
                </tr>
            </thead>

            <tbody x-data="{ isMouseDown: false, checkStatus: true }" @mousedown="isMouseDown = true" @mouseup.window="isMouseDown = false" class="divide-y divide-[var(--color-border-color)]">
                @forelse($roles as $item)
                    <tr wire:key="role-{{ $item->id }}" 
                        @mousedown="if(!$event.target.closest('button') && $event.target.tagName !== 'INPUT') { let cb = $el.querySelector('input[type=checkbox]'); checkStatus = !cb.checked; cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); }"
                        @mouseenter="if(isMouseDown && !$event.target.closest('button')) { let cb = $el.querySelector('input[type=checkbox]'); if(cb && cb.checked !== checkStatus) { cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }"
                        class="hover:bg-[var(--color-background)]/50 transition-all group cursor-pointer select-none">
                        
                        <td class="p-4 text-center">
                            <input type="checkbox" wire:model.live="selectedRoles" value="{{ $item->id }}" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)]">
                        </td>
                        @if(in_array('name', $selectedColumns))
                            <td class="p-4">
                                <div class="font-black text-[var(--color-text-main)] group-hover:text-[var(--color-primary)] text-sm md:text-base">{{ $item->name }}</div>
                                <div class="text-[10px] text-[var(--color-primary)] font-bold uppercase">{{ $item->permissions->count() }} Permissions</div>
                            </td>
                        @endif

                        {{-- បង្ហាញទិន្នន័យ Level (បន្ថែមថ្មី) --}}
                        @if(in_array('level', $selectedColumns))
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 bg-amber-500/10 text-amber-500 font-black rounded-full text-xs">Lv. {{ $item->level ?? 1 }}</span>
                            </td>
                        @endif

                        @if(in_array('guard_name', $selectedColumns))
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 bg-[var(--color-background)] border border-[var(--color-border-color)] rounded-full text-[10px] font-black text-[var(--color-text-muted)] uppercase">{{ $item->guard_name }}</span>
                            </td>
                        @endif

                        @if(in_array('created_at', $selectedColumns))
                            <td class="p-4">
                                <div class="text-sm font-bold text-[var(--color-text-main)]">{{ $item->created_at->format('d M, Y') }}</div>
                                <div class="text-[10px] font-black text-[var(--color-text-muted)] mt-0.5">{{ $item->created_at->format('h:i A') }}</div>
                            </td>
                        @endif

                        <td class="p-4 flex justify-center gap-2">
    
                            {{-- ប៊ូតុងទី ១៖ Manage Permissions (ពណ៌លឿង - ចាស់) --}}
                            <button wire:click="managePermissions({{ $item->id }})" title="Manage Permissions" class="p-2 rounded-lg bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                            </button>

                            {{-- ប៊ូតុងទី ២៖ Assignable Permissions (ពណ៌ស្វាយ - ថ្មី) --}}
                            <button wire:click="manageAssignablePermissions({{ $item->id }})" title="Assignable Permissions" class="p-2 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white transition-all">
                                {{-- ប្រើ Icon រូបមនុស្សមានសញ្ញាព្រួញចែកចាយ ឬ ខែល --}}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </button>

                            

                            <button wire:click="editRole({{ $item->id }})" title="Edit Role" class="p-2 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>

                            <button wire:click="confirmDelete({{ $item->id }})" title="Delete Role" class="p-2 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($selectedColumns) + 2 }}" class="p-16 text-center text-[var(--color-text-muted)] uppercase text-xs italic">{{ __('messages.no_data') ?? 'No Data' }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 bg-[var(--color-background)]/30 border-t border-[var(--color-border-color)]">
        {{ $roles->links('livewire.parts.pagination') }}
    </div>
</div>