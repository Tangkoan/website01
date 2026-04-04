<div class="hidden md:block bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm min-h-[300px] relative">
    {{-- Loading Overlay --}}
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
                    
                    @if(in_array('name', $selectedColumns) || in_array('email', $selectedColumns))
                        <th wire:click="sortBy('name')" class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest cursor-pointer hover:text-[var(--color-primary)] transition-colors">
                            <div class="flex items-center gap-2">
                                {{ __('messages.employee') ?? 'Employee' }}
                                @if($sortField === 'name') <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg> @endif
                            </div>
                        </th>
                    @endif
                    
                    @if(in_array('role', $selectedColumns))
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.role_and_level') ?? 'Role & Level' }}</th>
                    @endif
                    
                    @if(in_array('created_at', $selectedColumns))
                        <th wire:click="sortBy('created_at')" class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest cursor-pointer hover:text-[var(--color-primary)] transition-colors">
                            <div class="flex items-center gap-2">
                                {{ __('messages.created_date') ?? 'Created Date' }}
                                @if($sortField === 'created_at') <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg> @endif
                            </div>
                        </th>
                    @endif

                    @if(in_array('status', $selectedColumns))
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center">{{ __('messages.status') ?? 'Status' }}</th>
                    @endif
                    
                    <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center">{{ __('messages.actions') ?? 'Actions' }}</th>
                </tr>
            </thead>

            <tbody x-data="{ isMouseDown: false, checkStatus: true }" @mousedown="isMouseDown = true" @mouseup.window="isMouseDown = false" class="divide-y divide-[var(--color-border-color)]">
                @forelse($users as $user)
                    @php
                        // ✅ កែប្រែ៖ ប្រើអថេរដែលបោះពី Component មកស្រាប់ កុំអោយហៅ auth()->user() ម្តងហើយម្តងទៀត
                        $targetMaxLevel = $user->roles->max('level') ?? 0;
                        $isSelf = $user->id === auth()->id();
                        
                        $canManage = $isSelf || $isSuperAdmin || ($targetMaxLevel < $myMaxLevel);
                        $canToggleStatus = auth()->user()->can('update-user-status') && ($isSuperAdmin || ($targetMaxLevel < $myMaxLevel)) && !$isSelf;
                    @endphp
                    
                    <tr wire:key="user-desktop-{{ $user->id }}" 
                        @mousedown="if(!$event.target.closest('button') && !$event.target.closest('label') && $event.target.tagName !== 'INPUT') { let cb = $el.querySelector('input[type=checkbox]'); if(cb && !cb.disabled) { checkStatus = !cb.checked; cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }"
                        @mouseenter="if(isMouseDown && !$event.target.closest('button') && !$event.target.closest('label')) { let cb = $el.querySelector('input[type=checkbox]'); if(cb && !cb.disabled && cb.checked !== checkStatus) { cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }"
                        class="hover:bg-[var(--color-background)]/50 transition-all group cursor-pointer select-none">
                        
                        <td class="p-4 text-center">
                            <input type="checkbox" wire:model.live="selectedUsers" value="{{ $user->id }}" {{ ($isSelf && !$isSuperAdmin) ? 'disabled' : '' }} class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                        </td>
                        
                        @if(in_array('name', $selectedColumns) || in_array('email', $selectedColumns))
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 flex-shrink-0">
                                    @if($user->image)
                                        <img src="{{ str_starts_with($user->image, 'http') ? $user->image : asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover border border-[var(--color-primary)]/20 shadow-sm">
                                    @else
                                        <span class="h-10 w-10 rounded-full bg-[var(--color-primary)]/10 flex items-center justify-center text-[var(--color-primary)] font-black border border-[var(--color-primary)]/20">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    @if(in_array('name', $selectedColumns))
                                        <div class="font-black text-[var(--color-text-main)] group-hover:text-[var(--color-primary)] text-sm md:text-base">
                                            {{ $user->name }}
                                            @if($isSelf) <span class="ml-1 text-[9px] bg-green-500/10 text-green-500 px-1 rounded uppercase">You</span> @endif
                                        </div>
                                    @endif
                                    @if(in_array('email', $selectedColumns))
                                        <div class="text-[10px] text-[var(--color-text-muted)] font-bold">{{ $user->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        @endif
                        
                        @if(in_array('role', $selectedColumns))
                        <td class="p-4">
                            <div class="flex flex-col gap-1.5 justify-center">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $r) {{-- ✅ កែប្រែ៖ ប្រើ $user->roles ត្រង់ៗដើម្បីទាញយកអត្ថប្រយោជន៍ពី with('roles') កុំអោយ N+1 --}}
                                        <span class="px-2 py-0.5 bg-[var(--color-background)] border border-[var(--color-border-color)] rounded-full text-[10px] font-black text-[var(--color-text-muted)] uppercase">
                                            {{ $r->name }}
                                        </span>
                                    @endforeach
                                </div>
                                <div class="text-[10px] text-[var(--color-primary)] font-bold uppercase">Level: {{ $targetMaxLevel }}</div>
                            </div>
                        </td>
                        @endif

                        @if(in_array('created_at', $selectedColumns))
                        <td class="p-4">
                            <span class="text-[11px] font-bold text-[var(--color-text-muted)]">{{ $user->created_at ? $user->created_at->format('d M, Y') : 'N/A' }}</span>
                        </td>
                        @endif
                        
                        @if(in_array('status', $selectedColumns))
                            <td class="p-4 text-center">
                                {{-- ✅ បើគ្មានសិទ្ធិ ប្តូរ Cursor ទៅជាសញ្ញាហាមឃាត់ (cursor-not-allowed) --}}
                                <label class="relative inline-flex items-center {{ $canToggleStatus ? 'cursor-pointer' : 'cursor-not-allowed' }}" 
                                    title="{{ !$canToggleStatus ? (__('messages.no_permission_to_toggle') ?? 'No Permission or Restricted Level') : (__('messages.toggle_status') ?? 'Toggle Status') }}">
                                    
                                    {{-- ✅ ប្រើ wire:change ឱ្យលឿន និងបន្ថែម disabled បើគ្មានសិទ្ធិ --}}
                                    <input type="checkbox" wire:change="toggleStatus({{ $user->id }})" class="sr-only peer" {{ $user->status ? 'checked' : '' }} {{ !$canToggleStatus ? 'disabled' : '' }}>
                                    
                                    {{-- ✅ បើគ្មានសិទ្ធិ ធ្វើឱ្យពណ៌រាងស្រអាប់ (opacity-50) --}}
                                    <div class="w-9 h-5 bg-[var(--color-border-color)] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[var(--color-border-color)] after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--color-primary)] {{ !$canToggleStatus ? 'opacity-50' : '' }}"></div>
                                </label>
                            </td>
                        @endif
                        
                        <td class="p-4 flex justify-center gap-2 items-center h-full mt-2">
                            <x-auth-button permission="edit-user" 
                                wire:click="editUser({{ $user->id }})" 
                                title="Edit User" 
                                :disabled="!$canManage"
                                class="p-2 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[var(--color-primary)]/10 disabled:hover:text-[var(--color-primary)]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </x-auth-button>
                            
                            <x-auth-button permission="delete-user" 
                                wire:click="confirmDelete({{ $user->id }})" 
                                title="Delete User" 
                                :disabled="!$canManage"
                                class="p-2 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-red-500/10 disabled:hover:text-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </x-auth-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-16 text-center text-[var(--color-text-muted)] uppercase text-xs italic">{{ __('messages.no_data') ?? 'No Data' }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 bg-[var(--color-background)]/30 border-t border-[var(--color-border-color)]">
        {{ $users->links('livewire.parts.pagination') }}
    </div>
</div>