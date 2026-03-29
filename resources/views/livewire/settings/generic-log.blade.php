<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    
    {{-- Header --}}
    <div class="flex justify-between items-start sm:items-center gap-3 md:gap-4">
        <h2 class="flex-1 min-w-0 text-xl md:text-3xl font-black text-[var(--color-text-main)] tracking-tight flex items-center gap-2.5 md:gap-3">
            <span class="shrink-0 p-2 md:p-2.5 bg-blue-500/10 rounded-lg md:rounded-xl text-blue-500 text-lg md:text-2xl flex items-center justify-center">
                📋
            </span>
            <span class="leading-tight truncate">{{ $title }}</span>
        </h2>
        <a href="{{ route($backRoute) }}" wire:navigate class="shrink-0 p-2 sm:px-4 sm:py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg shadow-sm text-sm font-black text-[var(--color-text-main)] hover:brightness-95 flex items-center gap-2 transition-all focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/30 mt-0.5 sm:mt-0">
            <svg class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span class="hidden sm:block">{{ __('messages.back') }}</span>
        </a>
    </div>

    {{-- Filters & Bulk Actions --}}
    <div class="bg-[var(--color-card-bg)] p-3 rounded-xl border border-[var(--color-border-color)] shadow-sm flex flex-wrap justify-between items-center gap-4">
        <div class="relative w-full sm:w-96 group">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[var(--color-text-muted)] group-focus-within:text-[var(--color-primary)] transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] rounded-lg pl-10 pr-4 text-sm font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 outline-none transition-all" 
                placeholder="{{ __('messages.search_logs') }}">
        </div>

        @if(count($selectedLogs) > 0)
            <button wire:click="confirmBulkDelete" 
                class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-black rounded-lg shadow-sm transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                {{ __('messages.delete_selected') }} ({{ count($selectedLogs) }})
            </button>
        @endif
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden md:block bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm overflow-hidden min-h-[300px] relative">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[var(--color-background)]/50 border-b border-[var(--color-border-color)]">
                        <th class="p-4 w-12 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] bg-transparent checked:bg-[var(--color-primary)] cursor-pointer">
                        </th>
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.date_time') }}</th>
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.caused_by') }}</th>
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center">{{ __('messages.action') }}</th>
                        <th class="p-4 w-28 text-center text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                
                <tbody x-data="{ isMouseDown: false, checkStatus: true }" @mousedown="isMouseDown = true" @mouseup.window="isMouseDown = false" class="divide-y divide-[var(--color-border-color)]">
                    @forelse($activities as $log)
                        <tr wire:key="log-desktop-{{ $log->id }}" 
                            @mousedown="if(!$event.target.closest('button') && !$event.target.closest('label') && $event.target.tagName !== 'INPUT') { let cb = $el.querySelector('input[type=checkbox]'); if(cb && !cb.disabled) { checkStatus = !cb.checked; cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }"
                            @mouseenter="if(isMouseDown && !$event.target.closest('button') && !$event.target.closest('label')) { let cb = $el.querySelector('input[type=checkbox]'); if(cb && !cb.disabled && cb.checked !== checkStatus) { cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }"
                            class="hover:bg-[var(--color-background)]/50 transition-all group cursor-pointer select-none">
                            
                            <td class="p-4 text-center">
                                <input type="checkbox" wire:model.live="selectedLogs" value="{{ $log->id }}" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                            </td>

                            <td class="p-4">
                                <div class="font-bold text-[var(--color-text-main)] text-sm">{{ $log->created_at->format('d M, Y') }}</div>
                                <div class="text-[10px] font-black text-[var(--color-text-muted)] uppercase">{{ $log->created_at->format('h:i A') }}</div>
                            </td>

                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-full bg-[var(--color-primary)]/10 flex items-center justify-center font-black text-[var(--color-primary)] text-xs border border-[var(--color-primary)]/20">
                                        {{ $log->causer ? substr($log->causer->name, 0, 1) : 'S' }}
                                    </div>
                                    <div>
                                        <div class="font-black text-[var(--color-text-main)] text-sm">{{ $log->causer->name ?? __('messages.system') }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4 text-center">
                                @php
                                    $desc = strtolower($log->description);
                                    if (str_contains($desc, 'bulk') && str_contains($desc, 'delete')) {
                                        $colorClass = 'bg-orange-50 text-orange-600 border-orange-100 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/30';
                                    } elseif (str_contains($desc, 'bulk') && (str_contains($desc, 'edit') || str_contains($desc, 'update'))) {
                                        $colorClass = 'bg-teal-50 text-teal-600 border-teal-100 dark:bg-teal-500/10 dark:text-teal-400 dark:border-teal-500/30';
                                    } elseif (str_contains($desc, 'create')) {
                                        $colorClass = 'bg-green-50 text-green-600 border-green-100 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/30';
                                    } elseif (str_contains($desc, 'restore')) {
                                        $colorClass = 'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30';
                                    } elseif (str_contains($desc, 'delete')) {
                                        $colorClass = 'bg-red-50 text-red-600 border-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30';
                                    } elseif (str_contains($desc, 'update') || str_contains($desc, 'edit')) {
                                        $colorClass = 'bg-indigo-50 text-indigo-600 border-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/30';
                                    } else {
                                        $colorClass = 'bg-gray-50 text-gray-600 border-gray-100 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/30';
                                    }
                                @endphp
                                <span class="px-3 py-1 border rounded-full text-[10px] font-black uppercase tracking-wider {{ $colorClass }}">
                                    {{ $log->description }}
                                </span>
                            </td>

                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button wire:click.stop="viewDetails({{ $log->id }})" title="{{ __('messages.view') }}" class="p-2 text-[var(--color-text-muted)] hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)] rounded-lg transition-all focus:outline-none">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    
                                    <button wire:click.stop="confirmDelete({{ $log->id }})" title="{{ __('messages.delete') }}" class="p-2 text-[var(--color-text-muted)] hover:bg-red-500/10 hover:text-red-500 rounded-lg transition-all focus:outline-none">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-16 text-center text-[var(--color-text-muted)] uppercase text-xs italic opacity-50">{{ __('messages.no_logs_found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($activities->hasPages())
            <div class="p-4 bg-[var(--color-background)]/30 border-t border-[var(--color-border-color)]">
                {{ $activities->links('livewire.parts.pagination') }}
            </div>
        @endif
    </div>

    {{-- Mobile View --}}
    <div class="md:hidden space-y-3">
        @forelse($activities as $log)
            <div wire:click="viewDetails({{ $log->id }})" class="bg-[var(--color-card-bg)] p-4 rounded-xl border border-[var(--color-border-color)] shadow-sm cursor-pointer hover:border-[var(--color-primary)]/50 active:scale-[0.98] transition-all group relative">
                
                <div class="absolute top-4 right-4" wire:click.stop>
                    <input type="checkbox" wire:model.live="selectedLogs" value="{{ $log->id }}" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                </div>

                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 shrink-0 rounded-full bg-[var(--color-primary)]/10 flex items-center justify-center font-black text-[var(--color-primary)] text-sm border border-[var(--color-primary)]/20 mt-1">
                        {{ $log->causer ? substr($log->causer->name, 0, 1) : 'S' }}
                    </div>
                    <div class="pr-6">
                        <div class="font-black text-[var(--color-text-main)] text-sm">{{ $log->causer->name ?? __('messages.system') }}</div>
                        <div class="text-[10px] font-bold text-[var(--color-text-muted)] mt-0.5">{{ $log->created_at->format('d M, Y - h:i A') }}</div>
                        
                        @php
                            $desc = strtolower($log->description);
                            if (str_contains($desc, 'bulk') && str_contains($desc, 'delete')) {
                                $colorClass = 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400';
                            } elseif (str_contains($desc, 'bulk') && (str_contains($desc, 'edit') || str_contains($desc, 'update'))) {
                                $colorClass = 'bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400';
                            } elseif (str_contains($desc, 'create')) {
                                $colorClass = 'bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400';
                            } elseif (str_contains($desc, 'restore')) {
                                $colorClass = 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400';
                            } elseif (str_contains($desc, 'delete')) {
                                $colorClass = 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400';
                            } elseif (str_contains($desc, 'update') || str_contains($desc, 'edit')) {
                                $colorClass = 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400';
                            } else {
                                $colorClass = 'bg-gray-50 text-gray-600 dark:bg-gray-500/10 dark:text-gray-400';
                            }
                        @endphp
                        <div class="mt-2 inline-block px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $colorClass }}">
                            {{ $log->description }}
                        </div>
                    </div>
                </div>

                {{-- Action Icons Mobile --}}
                <div class="mt-4 pt-3 border-t border-[var(--color-border-color)] flex justify-end items-center gap-2">
                    <button class="p-1.5 text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    <button wire:click.stop="confirmDelete({{ $log->id }})" class="p-1.5 text-[var(--color-text-muted)] hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-[var(--color-card-bg)] p-10 rounded-xl border border-[var(--color-border-color)] text-center space-y-2">
                <span class="text-3xl opacity-50">📭</span>
                <span class="block text-[var(--color-text-muted)] text-sm font-bold opacity-70">{{ __('messages.no_logs_found') }}</span>
            </div>
        @endforelse
        
        @if($activities->hasPages())
            <div class="mt-4">
                {{ $activities->links('livewire.parts.pagination') }}
            </div>
        @endif
    </div>

    {{-- Modal View Details --}}
    @if($isModalOpen && $selectedActivity)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm animate-in fade-in duration-200">
            <div class="bg-[var(--color-card-bg)] rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden border border-[var(--color-border-color)]">
                <div class="p-4 border-b border-[var(--color-border-color)] flex justify-between items-center bg-[var(--color-background)]/50">
                    <h3 class="font-black text-lg text-[var(--color-text-main)]">{{ __('messages.log_details') }}</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-[var(--color-text-muted)] hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-4 sm:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    @if(isset($selectedActivity->properties['old']))
                        <div>
                            <label class="text-xs font-black text-red-500 uppercase tracking-widest mb-2 block">{{ __('messages.old_values') }}</label>
                            <pre class="bg-red-50 text-red-700 border-red-100 dark:bg-red-900/10 dark:text-red-400 dark:border-red-900/30 p-4 rounded-lg text-xs font-mono overflow-x-auto border">{{ json_encode($selectedActivity->properties['old'], JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                    @if(isset($selectedActivity->properties['attributes']))
                        <div>
                            <label class="text-xs font-black text-green-500 uppercase tracking-widest mb-2 block">{{ __('messages.new_values') }}</label>
                            <pre class="bg-green-50 text-green-700 border-green-100 dark:bg-green-900/10 dark:text-green-400 dark:border-green-900/30 p-4 rounded-lg text-xs font-mono overflow-x-auto border">{{ json_encode($selectedActivity->properties['attributes'], JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Custom Delete Modal Component --}}
    <x-modals.delete
        :isOpen="$showDeleteModal"
        onClose="closeDeleteModal"
        onConfirm="executeDelete"
        title="{{ __('messages.confirm_delete') }}"
        :message="$isBulkDelete ? __('messages.bulk_delete_confirm', ['count' => count($selectedLogs)]) : __('messages.single_delete_confirm')"
    />

</div>