<div class="hidden md:block bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm min-h-[300px] relative">
    <div wire:loading wire:target="getItemsProperty, reloadData" class="absolute inset-0 z-10 bg-[var(--color-card-bg)]/50 backdrop-blur-sm flex items-center justify-center rounded-xl">
        <svg class="animate-spin w-8 h-8 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[var(--color-background)]/50 border-b border-[var(--color-border-color)]">
                    <th class="p-4 w-16 text-center"><input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] cursor-pointer checked:bg-[var(--color-primary)]"></th>
                    @if(in_array('parent_id', $selectedColumns)) <th wire:click="sortBy('parent_id')" class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest cursor-pointer hover:text-[var(--color-primary)] transition-colors"><div class="flex items-center gap-2">{{ __('messages.parent_id') ?? 'Parent' }} @if($sortField === 'parent_id') <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg> @endif</div></th> @endif
                    @if(in_array('name', $selectedColumns)) <th wire:click="sortBy('name')" class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest cursor-pointer hover:text-[var(--color-primary)] transition-colors"><div class="flex items-center gap-2">{{ __('messages.name') ?? 'Name' }} @if($sortField === 'name') <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg> @endif</div></th> @endif
                    @if(in_array('status', $selectedColumns)) <th wire:click="sortBy('status')" class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest cursor-pointer hover:text-[var(--color-primary)] transition-colors"><div class="flex items-center gap-2">{{ __('messages.status') ?? 'Status' }} @if($sortField === 'status') <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg> @endif</div></th> @endif
                    @if(in_array('image', $selectedColumns)) <th wire:click="sortBy('image')" class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest cursor-pointer hover:text-[var(--color-primary)] transition-colors"><div class="flex items-center gap-2">{{ __('messages.image') ?? 'Image' }} @if($sortField === 'image') <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg> @endif</div></th> @endif
                    @if(in_array('images', $selectedColumns)) <th wire:click="sortBy('images')" class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest cursor-pointer hover:text-[var(--color-primary)] transition-colors"><div class="flex items-center gap-2">{{ __('messages.images') ?? 'Images' }} @if($sortField === 'images') <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg> @endif</div></th> @endif

                    <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center">{{ __('messages.actions') ?? 'Actions' }}</th>
                </tr>
            </thead>
            <tbody x-data="{ isMouseDown: false, checkStatus: true }" @mousedown="isMouseDown = true" @mouseup.window="isMouseDown = false" class="divide-y divide-[var(--color-border-color)]">
                @forelse($items as $item)
                    <tr wire:key="brand-desktop-{{ $item->id }}" @mousedown="if(!$event.target.closest('button') && !$event.target.closest('a') && !$event.target.closest('label') && $event.target.tagName !== 'INPUT' && $event.target.tagName !== 'SVG' && $event.target.tagName !== 'PATH') { let cb = $el.querySelector('.row-checkbox'); if(cb && !cb.disabled) { checkStatus = !cb.checked; cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }" @mouseenter="if(isMouseDown && !$event.target.closest('button') && !$event.target.closest('a') && !$event.target.closest('label') && $event.target.tagName !== 'INPUT' && $event.target.tagName !== 'SVG' && $event.target.tagName !== 'PATH') { let cb = $el.querySelector('.row-checkbox'); if(cb && !cb.disabled && cb.checked !== checkStatus) { cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }" class="hover:bg-[var(--color-background)]/50 transition-all group cursor-pointer select-none">
                        <td class="p-4 text-center"><input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" class="row-checkbox w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"></td>
                        @if(in_array('parent_id', $selectedColumns)) <td class="p-4 text-sm font-bold text-[var(--color-text-main)]">{{ $item->parent?->name ?? $item->parent?->title ?? $item->parent_id ?? 'N/A' }}</td> @endif
                        @if(in_array('name', $selectedColumns)) 
                        <td class="p-4 text-sm font-bold text-[var(--color-text-main)] transition-colors">
                            @php $pt = strip_tags($item->name); @endphp
                            {{ Str::limit($pt, 40) ?: '---' }}
                        </td> 
                        @endif
                        @if(in_array('status', $selectedColumns))
                        <td class="p-4 text-center whitespace-nowrap w-[1%]">
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" wire:change.stop="toggleField({{ $item->id }}, 'status')" class="sr-only peer" {{ $item->status ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-[var(--color-border-color)] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[var(--color-border-color)] after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--color-primary)] shrink-0"></div>
                            </label>
                        </td>
                        @endif
                        @if(in_array('image', $selectedColumns))
                        <td class="p-4 w-[1%] whitespace-nowrap">
                            @php 
                                $f = $item->image;
                                // 🌟 ឆែកមើលបើវាជា JSON String ត្រូវ Decode វាសិន
                                if(is_string($f) && (str_starts_with($f, '[') || str_starts_with($f, '{'))) {
                                    $f = json_decode($f, true);
                                }
                            @endphp
                            
                            @if($f) 
                                <div class="flex flex-wrap gap-2">
                                    @if(is_array($f))
                                        {{-- 🌟 បើវាជា Array បង្ហាញរូបទាំងអស់ (Loop) --}}
                                        @foreach($f as $img)
                                            <img src="{{ asset('storage/'.$img) }}" class="h-10 w-10 rounded-lg object-cover border-2 border-[var(--color-card-bg)] shadow-sm shrink-0">
                                        @endforeach
                                    @else
                                        {{-- 🌟 បើវាជារូបទោល --}}
                                        <img src="{{ asset('storage/'.$f) }}" class="h-10 w-10 rounded-lg object-cover border border-[var(--color-border-color)] shrink-0">
                                    @endif
                                </div>
                            @else 
                                <span class="text-[10px] text-[var(--color-text-muted)] italic">N/A</span> 
                            @endif
                        </td>
                        @endif
                        @if(in_array('images', $selectedColumns))
                        <td class="p-4 w-[1%] whitespace-nowrap">
                            @php 
                                $f = $item->images;
                                // 🌟 ឆែកមើលបើវាជា JSON String ត្រូវ Decode វាសិន
                                if(is_string($f) && (str_starts_with($f, '[') || str_starts_with($f, '{'))) {
                                    $f = json_decode($f, true);
                                }
                            @endphp
                            
                            @if($f) 
                                <div class="flex flex-wrap gap-2">
                                    @if(is_array($f))
                                        {{-- 🌟 បើវាជា Array បង្ហាញរូបទាំងអស់ (Loop) --}}
                                        @foreach($f as $img)
                                            <img src="{{ asset('storage/'.$img) }}" class="h-10 w-10 rounded-lg object-cover border-2 border-[var(--color-card-bg)] shadow-sm shrink-0">
                                        @endforeach
                                    @else
                                        {{-- 🌟 បើវាជារូបទោល --}}
                                        <img src="{{ asset('storage/'.$f) }}" class="h-10 w-10 rounded-lg object-cover border border-[var(--color-border-color)] shrink-0">
                                    @endif
                                </div>
                            @else 
                                <span class="text-[10px] text-[var(--color-text-muted)] italic">N/A</span> 
                            @endif
                        </td>
                        @endif

                        <td class="p-4 flex justify-center gap-2 items-center h-full mt-2">
                            <x-auth-button permission="edit-brand" wire:click.stop="editItem({{ $item->id }})" title="{{ __('messages.edit') ?? 'Edit' }}" class="p-2 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed relative z-20">
                                <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </x-auth-button>
                            <x-auth-button permission="delete-brand" wire:click.stop="confirmDelete({{ $item->id }})" title="{{ __('messages.delete') ?? 'Delete' }}" class="p-2 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed relative z-20">
                                <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </x-auth-button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="p-16 text-center text-[var(--color-text-muted)] uppercase text-xs italic opacity-50">{{ __('messages.no_data') ?? 'No Data Found' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 bg-[var(--color-background)]/30 border-t border-[var(--color-border-color)]">{{ $items->links('livewire.parts.pagination') }}</div>
</div>