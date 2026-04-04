<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    
    {{-- Header --}}
    <div class="flex justify-between items-start sm:items-center gap-3 md:gap-4 relative z-20 border-b border-[var(--color-border-color)] pb-4">
        <h2 class="flex-1 min-w-0 text-xl md:text-3xl font-black text-[var(--color-text-main)] tracking-tight flex items-center gap-2.5 md:gap-3">
            <span class="shrink-0 p-2 md:p-2.5 bg-red-500/10 rounded-lg md:rounded-xl text-red-500 text-lg md:text-2xl flex items-center justify-center">🗑️</span>
            <span class="leading-tight truncate">{{ $title }}</span>
        </h2>
    </div>

    <div class="flex flex-col md:flex-row gap-6">
        
        {{-- Sidebar Tabs --}}
        <div class="w-full md:w-64 shrink-0 relative z-20">
            <div class="flex flex-row md:flex-col gap-2 md:gap-3 overflow-x-auto md:overflow-visible pb-2 md:pb-0 snap-x [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                @foreach($this->trashModules as $key => $tab)
                    @can($tab['permissions']['view'])
                        <button wire:click="$set('activeTab', '{{ $key }}')" 
                            class="shrink-0 snap-start w-auto md:w-full text-left px-4 py-2.5 md:py-3 rounded-lg font-bold transition-all flex items-center gap-2.5 md:gap-3 
                            {{ $activeTab === $key ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] border border-[var(--color-primary)] shadow-sm' : 'bg-[var(--color-card-bg)] text-[var(--color-text-muted)] hover:bg-[var(--color-background)] border border-[var(--color-border-color)]' }}">
                            <span class="text-base">{{ $tab['icon'] }}</span>
                            <span class="whitespace-nowrap">{{ $tab['label'] }}</span>
                        </button>
                    @endcan
                @endforeach
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 space-y-4">
            
            {{-- Filters & Bulk Actions --}}
            <div class="bg-[var(--color-card-bg)] p-3 rounded-xl border border-[var(--color-border-color)] shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 relative z-10">
                <div class="relative w-full sm:w-96 group">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[var(--color-text-muted)] group-focus-within:text-[var(--color-primary)] transition-colors"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>
                    <input type="text" wire:model.live.debounce.300ms="searchTerm" class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg pl-10 pr-4 text-sm font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 outline-none transition-all" placeholder="{{ __('messages.search_deleted') ?? 'Search deleted items...' }}">
                </div>

                @if(count($selectedItems) > 0)
                    <div class="w-full sm:w-auto flex items-center justify-between gap-2 p-2 bg-[var(--color-primary)]/10 rounded-lg border border-[var(--color-primary)]/20 animate-in fade-in zoom-in-95 duration-200">
                        <span class="text-sm font-black text-[var(--color-primary)] px-2">{{ count($selectedItems) }} Selected</span>
                        <div class="flex items-center gap-2">
                            
                            {{-- 🔒 ហៅយកសិទ្ធិពី Master Config មកប្រើផ្ទាល់ --}}
                            <x-auth-button permission="{{ $this->trashModules[$activeTab]['permissions']['restore'] }}" wire:click="restore()" class="px-4 py-2 bg-green-50 hover:bg-green-600 hover:text-white text-green-600 text-xs font-black rounded-md transition-colors shadow-sm flex items-center gap-1.5 border border-green-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Restore
                            </x-auth-button>
                            
                            <x-auth-button permission="{{ $this->trashModules[$activeTab]['permissions']['delete'] }}" wire:click="confirmForceDelete()" class="px-4 py-2 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 text-xs font-black rounded-md transition-colors shadow-sm flex items-center gap-1.5 border border-red-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Delete
                            </x-auth-button>

                        </div>
                    </div>
                @endif
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden md:block bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm min-h-[300px] relative z-30 overflow-visible">
                <div class="w-full overflow-visible">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[var(--color-background)]/50 border-b border-[var(--color-border-color)]">
                                <th class="p-4 w-16 text-center"><input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] cursor-pointer"></th>
                                <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">Information</th>
                                <th class="p-4 text-[10px] font-black text-red-500 uppercase tracking-widest text-center">Deleted At</th>
                                <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-border-color)]">
                            @forelse($items as $item)
                                <tr class="hover:bg-[var(--color-background)]/50 transition-all group">
                                    <td class="p-4 text-center"><input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] cursor-pointer"></td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            @if($activeTab === 'user')
                                                @if($item->image)
                                                    <img src="{{ asset('storage/profile-photos/' . $item->image) }}" class="h-10 w-10 rounded-full object-cover border border-[var(--color-border-color)] shadow-sm">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-[var(--color-primary)]/10 flex items-center justify-center font-black text-[var(--color-primary)] text-xs border border-[var(--color-primary)]/20">{{ substr($item->name, 0, 1) }}</div>
                                                @endif
                                            @endif
                                            <div>
                                                <div class="font-black text-[var(--color-text-main)] group-hover:text-[var(--color-primary)] transition-colors">{{ $item->name }}</div>
                                                <div class="text-[10px] text-[var(--color-text-muted)] font-bold">{{ $activeTab === 'user' ? $item->email : ($item->guard_name ?? 'web') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="text-red-500 font-bold text-sm">{{ $item->deleted_at->format('d M, Y') }}</div>
                                        <div class="text-[10px] text-[var(--color-text-muted)] font-black uppercase">{{ $item->deleted_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="p-4 flex justify-end gap-2">
                                        
                                        <x-auth-button permission="{{ $this->trashModules[$activeTab]['permissions']['restore'] }}" wire:click="restore({{ $item->id }})" title="Restore" class="p-2 rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-all border border-green-100 dark:bg-green-500/10 dark:border-green-500/30 dark:hover:bg-green-500 dark:hover:text-white">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        </x-auth-button>
                                        
                                        <x-auth-button permission="{{ $this->trashModules[$activeTab]['permissions']['delete'] }}" wire:click="confirmForceDelete({{ $item->id }})" title="Delete Permanently" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all border border-red-100 dark:bg-red-500/10 dark:border-red-500/30 dark:hover:bg-red-500 dark:hover:text-white">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </x-auth-button>

                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="p-16 text-center text-[var(--color-text-muted)] uppercase text-xs italic opacity-50">{{ __('messages.no_deleted_data') ?? 'No deleted data found' }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 bg-[var(--color-background)]/30 border-t border-[var(--color-border-color)]">
                    {{ $items->links('livewire.parts.pagination') }}
                </div>
            </div>

            {{-- Mobile View --}}
            <div class="md:hidden space-y-3 relative z-30">
                @forelse($items as $item)
                    <div class="bg-[var(--color-card-bg)] p-4 rounded-xl border border-[var(--color-border-color)] shadow-sm space-y-3">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" class="w-4 h-4 rounded text-[var(--color-primary)] border-2 border-[var(--color-text-main)] cursor-pointer">
                            <div class="font-black text-[var(--color-text-main)] truncate flex-1">{{ $item->name }}</div>
                        </div>
                        <div class="flex justify-between items-center border-t border-[var(--color-border-color)] pt-3">
                            <span class="text-xs font-bold text-red-500">{{ $item->deleted_at->format('d M, Y') }}</span>
                            <div class="flex gap-2">
                                
                                <x-auth-button permission="{{ $this->trashModules[$activeTab]['permissions']['restore'] }}" wire:click="restore({{ $item->id }})" class="p-2 bg-green-50 text-green-600 rounded-lg border border-green-100 dark:bg-green-500/10 dark:border-green-500/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                </x-auth-button>
                                
                                <x-auth-button permission="{{ $this->trashModules[$activeTab]['permissions']['delete'] }}" wire:click="confirmForceDelete({{ $item->id }})" class="p-2 bg-red-50 text-red-600 rounded-lg border border-red-100 dark:bg-red-500/10 dark:border-red-500/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </x-auth-button>

                            </div>
                        </div>
                    </div>
                @empty
                     <div class="bg-[var(--color-card-bg)] p-10 rounded-xl border border-[var(--color-border-color)] text-center"><span class="text-[var(--color-text-muted)] text-sm font-bold opacity-70">{{ __('messages.no_deleted_data') ?? 'No deleted data found' }}</span></div>
                @endforelse
                <div class="mt-4">{{ $items->links('livewire.parts.pagination') }}</div>
            </div>

        </div>
    </div>

    {{-- Modal Confirm Delete --}}
    <x-modals.delete 
        :isOpen="$isDeleteModalOpen" 
        onClose="$set('isDeleteModalOpen', false)" 
        onConfirm="executeDelete" 
        message="{{ $deleteId ? 'This item will be permanently deleted.' : 'All selected items will be permanently removed from the system.' }}" 
    />
</div>