<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-[var(--color-text-main)] tracking-tight flex items-center gap-3">
                <span class="p-2 bg-[var(--color-primary)]/10 rounded-lg md:rounded-xl text-[var(--color-primary)] text-xl md:text-2xl">🔐</span>
                {{ __('messages.permissions') ?? 'Permissions' }}
            </h2>
        </div>
        <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0 relative">
            
            {{-- Column Picker Button --}}
            {{-- Column Picker Button (ប្រើ Alpine.js សម្រាប់ Click Outside) --}}
            <div x-data="{ showColumns: false }" class="relative">
                <button @click="showColumns = !showColumns" @click.outside="showColumns = false" type="button" class="p-2 md:p-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-[var(--color-text-main)] hover:brightness-95 dark:hover:brightness-110 transition-all flex-shrink-0 flex items-center gap-2">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                </button>
                
                {{-- Column Picker Dropdown --}}
                <div x-show="showColumns" 
                     style="display: none;"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute left-0 mt-2 w-48 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-xl shadow-2xl p-3 z-[100] origin-top-right">
                    <h4 class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest mb-2 border-b border-[var(--color-border-color)] pb-2">Show Columns</h4>
                    <div class="space-y-2">
                        @foreach($availableColumns as $key => $label)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" wire:model.live="selectedColumns" value="{{ $key }}" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/30 bg-transparent dark:bg-[var(--color-background)] checked:bg-[var(--color-primary)] checked:border-[var(--color-primary)] transition-all cursor-pointer">
                                <span class="text-sm font-bold text-[var(--color-text-main)] group-hover:text-[var(--color-primary)] transition-colors">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- ប៊ូតុងចូលទៅមើល Activity Log --}}
            @can('view-permission-logs') {{-- អាចប្ដូរឈ្មោះសិទ្ធិនេះតាមការចង់បាន --}}
                <a href="/settings/permissions/logs" wire:navigate class="p-2 md:p-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-blue-500 hover:bg-blue-500 hover:text-white transition-all flex-shrink-0 flex items-center gap-2" title="Activity Logs / កំណត់ត្រាសកម្មភាព">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="hidden md:inline-block text-sm font-black">Logs</span>
                </a>
            @endcan

            {{-- Reload Button with Animation --}}
            <button wire:click="reloadData" class="p-2 md:p-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-[var(--color-text-main)] hover:brightness-95 dark:hover:brightness-110 transition-all flex-shrink-0">
                <svg wire:loading.class="animate-spin text-[var(--color-primary)]" wire:target="reloadData" class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>

            {{-- ប៊ូតុងចូលទៅធុងសំរាម (Trash Button) --}}
            @can('delete-permission')
                <a href="/settings/permissions/trash" wire:navigate class="px-3 py-2 md:px-4 md:py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-red-500 hover:bg-red-500 hover:text-white transition-all flex-shrink-0 flex items-center gap-2" title="Trash / ធុងសំរាម">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span class="hidden md:inline-block text-sm font-black">{{ __('messages.permissions_trash') ?? 'ធុងសំរាម' }}</span>
                </a>
            @endcan
            
            @can('create-permission')
                <button wire:click="openModal" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 md:px-5 py-2 md:py-2.5 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-black rounded-lg shadow-lg shadow-[var(--color-primary)]/20 hover:brightness-110 active:scale-95 transition-all text-sm">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('messages.add_new') ?? 'Add New' }}
                </button>
            @endcan
        </div>
    </div>

    <div class="bg-[var(--color-card-bg)] p-3 rounded-xl border border-[var(--color-border-color)] shadow-sm flex flex-col lg:flex-row items-center justify-between gap-4">
        <div class="relative w-full lg:w-96 group">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[var(--color-text-muted)] group-focus-within:text-[var(--color-primary)] transition-colors pointer-events-none">
                <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                class="w-full h-10 md:h-11 bg-[var(--color-background)] border border-transparent focus:border-[var(--color-primary)]/50 text-[var(--color-text-main)] rounded-lg pl-10 pr-4 text-sm font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 transition-all outline-none" 
                placeholder="{{ __('messages.search') ?? 'Search' }}...">
        </div>

        @if(count($selectedPermissions) > 0)
            <div class="flex flex-col sm:flex-row items-center gap-2 p-1.5 bg-[var(--color-primary)]/10 dark:bg-[var(--color-primary)]/5 rounded-lg border border-[var(--color-primary)]/20 animate-in slide-in-from-right-4 w-full lg:w-auto">
                <span class="text-xs font-black text-[var(--color-primary)] px-3 uppercase w-full sm:w-auto text-center py-1 sm:py-0">{{ count($selectedPermissions) }} {{ __('messages.selected') ?? 'Selected' }}</span>
                <div class="flex gap-2 w-full sm:w-auto">
                    @can('bulk-edit-permission')
                        <button wire:click="bulkEdit" class="flex-1 sm:flex-none px-3 py-2 bg-amber-500 text-white text-xs font-black rounded-md hover:bg-amber-600 transition shadow-sm">{{ __('messages.bulk_edit') ?? 'Bulk Edit' }}</button>
                    @endcan
                    
                    @can('bulk-delete-permission')
                        <button wire:click="confirmDelete()" class="flex-1 sm:flex-none px-3 py-2 bg-red-500 text-white text-xs font-black rounded-md hover:bg-red-600 transition shadow-sm">{{ __('messages.bulk_delete') ?? 'Bulk Delete' }}</button>
                    @endcan
                </div>
            </div>
        @endif
    </div>

    <div>
        <div class="hidden md:block bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm min-h-[300px]">
            
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
                                <div class="flex items-center gap-2">{{ __('messages.name') ?? 'Name' }} @if($sortField === 'name') <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg> @endif</div>
                            </th>
                            @endif
                            
                            @if(in_array('guard_name', $selectedColumns))
                            <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center">{{ __('messages.guard') ?? 'Guard' }}</th>
                            @endif

                            @if(in_array('created_at', $selectedColumns))
                            <th wire:click="sortBy('created_at')" class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center cursor-pointer hover:text-[var(--color-primary)] transition-colors">
                                <div class="flex items-center justify-center gap-2">{{ __('messages.created_at') ?? 'Created At' }} @if($sortField === 'created_at') <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg> @endif</div>
                            </th>
                            @endif
                            
                            @canany(['edit-permission', 'delete-permission'])
                            <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-right">{{ __('messages.actions') ?? 'Actions' }}</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody x-data="{ isMouseDown: false, checkStatus: true }" 
                        @mousedown="isMouseDown = true" 
                        @mouseup.window="isMouseDown = false"
                        class="divide-y divide-[var(--color-border-color)] relative">
                        {{-- Loading Overlay --}}
                        <div wire:loading wire:target="getPermissionsProperty" class="absolute inset-0 z-10 bg-[var(--color-card-bg)]/50 backdrop-blur-sm flex items-center justify-center">
                            <svg class="animate-spin w-8 h-8 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </div>

                        @forelse($permissions as $item)
                            <tr @mousedown="
        if(!$event.target.closest('button')) {
            let cb = $el.querySelector('input[type=checkbox]');
            if(cb) {
                if($event.target.tagName !== 'INPUT') {
                    checkStatus = !cb.checked;
                    cb.checked = checkStatus;
                    cb.dispatchEvent(new Event('change'));
                } else {
                    checkStatus = !cb.checked;
                }
            }
        }
    "
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

                                @if(in_array('created_at', $selectedColumns))
                                <td class="p-4 text-center">
                                    <span class="text-[10px] font-bold text-[var(--color-text-muted)] mt-1 uppercase tracking-tighter">{{ $item->created_at->format('d M, Y') }}</span>
                                </td>
                                @endif
                                
                                @canany(['edit-permission', 'delete-permission'])
                                <td class="p-4 flex justify-end gap-2">
                                    @can('edit-permission')
                                    <button wire:click="editPermission({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-[var(--color-primary-text)] dark:bg-[var(--color-background)] dark:border-[var(--color-border-color)] dark:hover:bg-[var(--color-primary)] dark:hover:border-transparent">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    @endcan
                                    
                                    
                                    @can('delete-permission')
                                    <button wire:click="confirmDelete({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-red-50 text-red-500 hover:bg-red-500 hover:text-white dark:bg-[var(--color-background)] dark:border-[var(--color-border-color)] dark:hover:bg-red-500 dark:hover:border-transparent">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    @endcan
                                </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-16 text-center text-[var(--color-text-muted)] font-black uppercase text-xs tracking-widest italic opacity-50">{{ __('messages.no_data') ?? 'No Data' }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 md:p-5 bg-[var(--color-background)]/30 border-t border-[var(--color-border-color)]">
                {{ $permissions->links('livewire.parts.pagination') }}
            </div>
        </div>

        {{-- Mobile View Update accordingly --}}
        <div class="md:hidden space-y-3">
            @canany(['bulk-edit-permission', 'bulk-delete-permission'])
            <div class="flex items-center gap-3 px-2 mb-1">
                <input type="checkbox" wire:model.live="selectAll" id="selectAllMobile" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/30 bg-transparent cursor-pointer">
                <label for="selectAllMobile" class="text-sm font-black text-[var(--color-text-main)]">{{ __('messages.select_all') ?? 'Select All' }}</label>
            </div>
            @endcanany

            @forelse($permissions as $item)
                    <div x-data @click="if(!$event.target.closest('button') && $event.target.tagName !== 'INPUT') $el.querySelector('input[type=checkbox]').click()" class="bg-[var(--color-card-bg)] p-4 rounded-xl shadow-sm border border-[var(--color-border-color)] flex flex-col gap-3 cursor-pointer hover:bg-[var(--color-background)]/50 transition-all">                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            @canany(['bulk-edit-permission', 'bulk-delete-permission'])
                            <input type="checkbox" wire:model.live="selectedPermissions" value="{{ $item->id }}" class="w-4 h-4 mt-1 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/30 bg-transparent cursor-pointer flex-shrink-0">
                            @endcanany
                            <div class="min-w-0">
                                @if(in_array('name', $selectedColumns))
                                    <h4 class="font-black text-[var(--color-text-main)] text-base truncate">{{ $item->name }}</h4>
                                @endif
                                @if(in_array('guard_name', $selectedColumns))
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-[var(--color-background)] border border-[var(--color-text-main)] rounded-md text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ $item->guard_name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @canany(['edit-permission', 'delete-permission'])
                    <div class="border-t border-dashed border-[var(--color-border-color)] w-full"></div>
                    <div class="flex justify-between items-center">
                        @if(in_array('created_at', $selectedColumns))
                            <span class="text-[10px] font-bold text-[var(--color-text-muted)] uppercase tracking-widest">{{ $item->created_at->format('d M, Y') }}</span>
                        @else
                            <span></span>
                        @endif
                        <div class="flex gap-2">
                            @can('edit-permission')
                            <button wire:click="editPermission({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            @endcan
                            
                            @can('delete-permission')
                            <button wire:click="confirmDelete({{ $item->id }})" class="p-2 rounded-lg transition-all border border-transparent bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            @endcan
                        </div>
                    </div>
                    @endcanany
                </div>
            @empty
                <div class="bg-[var(--color-card-bg)] p-8 rounded-xl border border-[var(--color-border-color)] text-center text-[var(--color-text-muted)] font-black uppercase tracking-widest text-xs italic">{{ __('messages.no_data') ?? 'No Data' }}</div>
            @endforelse

            <div class="w-full flex justify-center mt-4">
                {{ $permissions->links('livewire.parts.pagination') }}
            </div>
        </div>
    </div>

    {{-- ការហៅ Component Reusable Modal Delete មកប្រើ --}}
    <x-modals.delete 
        :isOpen="$isDeleteModalOpen" 
        onClose="$set('isDeleteModalOpen', false)" 
        onConfirm="executeDelete" 
        message="{!! $deleteId ? '' : __('messages.bulk_delete_warning', ['count' => '<span class=\'text-red-500 font-black\'>' . count($selectedPermissions) . '</span>']) !!}"
    />

    @if($isBulkEditModalOpen)
    {{-- ... កូដ Bulk Edit Modal ចាស់រក្សាទុកដដែល ... --}}
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 dark:bg-black/60 animate-in fade-in duration-300">
            <div class="bg-[var(--color-card-bg)] w-full max-w-4xl rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden flex flex-col md:flex-row max-h-[90vh]">
                <div class="w-full md:w-1/3 bg-[var(--color-background)] border-r border-[var(--color-border-color)] p-5 overflow-y-auto max-h-[35vh] md:max-h-full">
                    <h3 class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest mb-3">{{ __('messages.editing') ?? 'Editing' }} {{ count($selectedItemsQueue) }} {{ __('messages.items') ?? 'Items' }}</h3>
                    <div class="space-y-2">
                        @foreach($selectedItemsQueue as $index => $item)
                            <button wire:click="jumpToBulkItem({{ $index }})" 
                                class="w-full text-left p-2.5 md:p-3 rounded-lg font-bold text-xs md:text-sm transition-all flex items-center justify-between
                                {{ $currentBulkIndex === $index ? 'bg-[var(--color-primary)] text-[var(--color-primary-text)] shadow-md shadow-[var(--color-primary)]/20 border-transparent' : 'bg-[var(--color-card-bg)] text-[var(--color-text-main)] hover:border-[var(--color-primary)] border border-[var(--color-border-color)]' }}">
                                <span class="truncate pr-2">{{ $item['name'] }}</span>
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
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.name') ?? 'Name' }}</label>
                            <input type="text" wire:model="bulkItemName" class="w-full h-10 md:h-11 bg-[var(--color-background)] border border-transparent focus:border-[var(--color-primary)]/50 text-[var(--color-text-main)] rounded-lg px-3 md:px-4 font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 transition-all outline-none text-sm">
                            @error('bulkItemName') <span class="text-red-500 text-[10px] font-bold italic">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.guard') ?? 'Guard' }}</label>
                            <select wire:model="bulkItemGuard" class="w-full h-10 md:h-11 bg-[var(--color-background)] border border-transparent focus:border-[var(--color-primary)]/50 text-[var(--color-text-main)] rounded-lg px-3 md:px-4 font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 transition-all outline-none text-sm">
                                <option value="web">web</option><option value="api">api</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 md:mt-8 flex gap-3">
                        <button wire:click="skipBulkItem" class="flex-1 h-10 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-black rounded-lg hover:brightness-95 dark:hover:brightness-110 transition-all uppercase tracking-widest text-[10px] shadow-sm">{{ __('messages.skip') ?? 'Skip' }}</button>
                        <button wire:click="saveAndNextBulkItem" class="flex-[2] h-10 bg-[var(--color-primary)] text-[var(--color-primary-text)] font-black rounded-lg shadow-xl shadow-[var(--color-primary)]/20 hover:brightness-110 active:scale-95 transition-all uppercase tracking-widest text-[10px]">{{ __('messages.save_next') ?? 'Save & Next' }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($isModalOpen)
    {{-- ... កូដ Add/Edit Modal ចាស់រក្សាទុកដដែល ... --}}
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
</div>