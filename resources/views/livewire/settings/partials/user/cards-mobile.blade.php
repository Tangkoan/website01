<div class="md:hidden space-y-3">
    <div class="flex items-center gap-3 px-2 mb-1">
        <input type="checkbox" wire:model.live="selectAll" id="selectAllMobile" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] bg-transparent cursor-pointer">
        <label for="selectAllMobile" class="text-sm font-black text-[var(--color-text-main)]">{{ __('messages.select_all') ?? 'Select All' }}</label>
    </div>

    @forelse($users as $user)
        @php
            $targetMaxLevel = $user->roles->max('level') ?? 0;
            $isSelf = $user->id === auth()->id();
            $isSuperAdmin = auth()->user()->hasRole('Super Admin');
            
            $canEdit = $isSelf || $isSuperAdmin || ($targetMaxLevel < $myMaxLevel);
            $canDelete = $isSelf || $isSuperAdmin || ($targetMaxLevel < $myMaxLevel);
        @endphp
        
        <div wire:key="user-mobile-{{ $user->id }}" x-data @click="if(!$event.target.closest('button') && !$event.target.closest('label') && $event.target.tagName !== 'INPUT') { let cb = $el.querySelector('input[type=checkbox]'); if(cb && !cb.disabled) cb.click() }" class="bg-[var(--color-card-bg)] p-4 rounded-xl shadow-sm border border-[var(--color-border-color)] flex flex-col gap-3 cursor-pointer hover:bg-[var(--color-background)]/50 transition-all">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <input type="checkbox" wire:model.live="selectedUsers" value="{{ $user->id }}" {{ ($isSelf && !$isSuperAdmin) ? 'disabled' : '' }} class="w-4 h-4 mt-1 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] bg-transparent disabled:opacity-50 cursor-pointer flex-shrink-0">
                    
                    @if(in_array('name', $selectedColumns) || in_array('email', $selectedColumns))
                    <div class="h-10 w-10 flex-shrink-0 mt-0.5">
                        @if($user->image)
                            <img src="{{ str_starts_with($user->image, 'http') ? $user->image : asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover border border-[var(--color-primary)]/20 shadow-sm">
                        @else
                            <span class="h-10 w-10 rounded-full bg-[var(--color-primary)]/10 flex items-center justify-center text-[var(--color-primary)] font-black border border-[var(--color-primary)]/20">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    @endif
                    
                    <div class="min-w-0 flex-1">
                        @if(in_array('name', $selectedColumns))
                            <h4 class="font-black text-[var(--color-text-main)] text-base truncate">
                                {{ $user->name }}
                                @if($isSelf) <span class="ml-1 text-[9px] bg-green-500/10 text-green-500 px-1 rounded uppercase">You</span> @endif
                            </h4>
                        @endif
                        
                        @if(in_array('email', $selectedColumns))
                            <div class="text-[10px] text-[var(--color-text-muted)] font-bold truncate">{{ $user->email }}</div>
                        @endif
                        
                        @if(in_array('role', $selectedColumns))
                        <div class="flex flex-wrap items-center gap-1 mt-2">
                            @foreach($user->getRoleNames() as $r)
                                <span class="px-2 py-0.5 bg-[var(--color-background)] border border-[var(--color-border-color)] rounded-md text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ $r }}</span>
                            @endforeach
                            <span class="px-2 py-0.5 bg-amber-500/10 text-amber-500 rounded-md text-[10px] font-black uppercase tracking-widest">Lv. {{ $targetMaxLevel }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="border-t border-dashed border-[var(--color-border-color)] w-full"></div>
            
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    @if(in_array('status', $selectedColumns))
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:click="toggleStatus({{ $user->id }})" class="sr-only peer" {{ $user->status ? 'checked' : '' }} {{ (!$canEdit || $isSelf) ? 'disabled' : '' }}>
                            <div class="w-9 h-5 bg-[var(--color-border-color)] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[var(--color-border-color)] after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--color-primary)] {{ (!$canEdit || $isSelf) ? 'opacity-50 cursor-not-allowed' : '' }}"></div>
                        </label>
                    @endif

                    @if(in_array('created_at', $selectedColumns))
                        <span class="text-[10px] font-bold text-[var(--color-text-muted)] uppercase tracking-widest">{{ $user->created_at ? $user->created_at->format('d M, Y') : 'N/A' }}</span>
                    @endif
                </div>
                
                <div class="flex gap-2">
                    @if($canEdit)
                    <button wire:click="editUser({{ $user->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>
                    @endif
                    
                    @if($canDelete)
                    <button wire:click="confirmDelete({{ $user->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-[var(--color-card-bg)] p-8 rounded-xl border border-[var(--color-border-color)] text-center text-[var(--color-text-muted)] font-black uppercase tracking-widest text-xs italic">{{ __('messages.no_data') ?? 'No Data' }}</div>
    @endforelse

    <div class="w-full flex justify-center mt-4">
        {{ $users->links('livewire.parts.pagination') }}
    </div>
</div>