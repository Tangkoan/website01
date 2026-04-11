{{-- 🌟 ផ្លាស់ប្តូរ x-data មកខាងក្រៅបំផុត ដើម្បីឱ្យ Hover Drag ស្គាល់គ្រប់ Tbody --}}
<div x-data="{ 
        isMouseDown: false, 
        checkStatus: true,
        initSortable() {
            if (typeof Sortable === 'undefined') {
                let script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js';
                script.onload = () => this.applySortable();
                document.head.appendChild(script);
            } else {
                this.applySortable();
            }
        },
        applySortable() {
            if (this.$refs.mainTable) {
                if(this.$refs.mainTable._sortable) this.$refs.mainTable._sortable.destroy();
                this.$refs.mainTable._sortable = Sortable.create(this.$refs.mainTable, {
                    draggable: 'tbody.parent-group',
                    handle: '.parent-drag-handle',
                    animation: 150,
                    ghostClass: 'bg-[var(--color-primary)]/10',
                    onEnd: (evt) => {
                        let items = Array.from(this.$refs.mainTable.querySelectorAll('tbody.parent-group')).map((tb, index) => ({ id: tb.dataset.id, order: index + 1 }));
                        $wire.updateItemOrder(items); 
                    }
                });
            }

            document.querySelectorAll('.child-sortable').forEach(el => {
                if(el._sortable) el._sortable.destroy(); 
                el._sortable = Sortable.create(el, {
                    draggable: 'tr.child-row',
                    handle: '.child-drag-handle',
                    animation: 150,
                    ghostClass: 'bg-[var(--color-primary)]/10',
                    onEnd: (evt) => {
                        let items = Array.from(el.querySelectorAll('tr.child-row')).map((row, index) => ({ id: row.dataset.id, order: index + 1 }));
                        $wire.updateItemOrder(items); 
                    }
                });
            });
        }
    }" 
    @mousedown="isMouseDown = true" 
    @mouseup.window="isMouseDown = false"
    x-init="initSortable(); Livewire.hook('morph.updated', () => { setTimeout(() => applySortable(), 50) });"
    class="hidden md:block bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm min-h-[300px] relative">
    
    <div wire:loading wire:target="getItemsProperty, reloadData, updateItemOrder" class="absolute inset-0 z-10 bg-[var(--color-card-bg)]/50 backdrop-blur-sm flex items-center justify-center rounded-xl">
        <svg class="animate-spin w-8 h-8 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
    </div>

    <div class="overflow-x-auto pb-10">
        <table class="w-full text-left border-collapse" x-ref="mainTable">
            <thead>
                <tr class="bg-[var(--color-background)]/50 border-b border-[var(--color-border-color)]">
                    <th class="p-4 w-16 text-center"><input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] cursor-pointer checked:bg-[var(--color-primary)]"></th>
                    @if(in_array('name', $selectedColumns)) <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.name')}}</th> @endif
                    @if(in_array('url', $selectedColumns)) <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.url') ?? 'Url' }}</th> @endif
                    @if(in_array('icon', $selectedColumns)) <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.icon') ?? 'Icon' }}</th> @endif
                    @if(in_array('permission', $selectedColumns)) <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.permission') ?? 'Permission' }}</th> @endif
                    @if(in_array('is_active', $selectedColumns)) <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.is_active') ?? 'Status' }}</th> @endif
                    <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center w-32">{{ __('messages.actions') ?? 'Actions' }}</th>
                </tr>
            </thead>
            
            @forelse($items as $item)
                @if(empty($item->parent_id))
                    @php 
                        $children = \App\Models\Sidebar::where('parent_id', $item->id)->orderBy('order', 'asc')->get(); 
                        $hasChildren = $children->count() > 0;
                        $isExpanded = in_array($item->id, $expandedItems); // 🌟 ឆែកមើលថាតើមេនេះបើកឬអត់
                    @endphp
                    
                    <tbody class="parent-group" data-id="{{ $item->id }}" wire:key="tbody-parent-{{ $item->id }}-{{ time() }}">
                        {{-- 🌟 បន្ថែម Hover Event នៅលើ Row មេ --}}
                        <tr @mousedown="if(!$event.target.closest('button') && !$event.target.closest('a') && !$event.target.closest('label') && $event.target.tagName !== 'INPUT' && $event.target.tagName !== 'SVG' && $event.target.tagName !== 'PATH' && !$event.target.closest('.parent-drag-handle')) { let cb = $el.querySelector('.row-checkbox'); if(cb && !cb.disabled) { checkStatus = !cb.checked; cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }" 
                            @mouseenter="if(isMouseDown && !$event.target.closest('button') && !$event.target.closest('a') && !$event.target.closest('label') && $event.target.tagName !== 'INPUT' && $event.target.tagName !== 'SVG' && $event.target.tagName !== 'PATH' && !$event.target.closest('.parent-drag-handle')) { let cb = $el.querySelector('.row-checkbox'); if(cb && !cb.disabled && cb.checked !== checkStatus) { cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }" 
                            class="bg-[var(--color-card-bg)] hover:bg-[var(--color-background)]/50 transition-colors border-b border-[var(--color-border-color)] select-none">
                            
                            <td class="p-4 text-center"><input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" class="row-checkbox w-4 h-4 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] cursor-pointer"></td>
                            
                            @if(in_array('name', $selectedColumns)) 
                            <td class="p-4 text-sm font-black text-[var(--color-text-main)] uppercase tracking-wide">
                                <div class="flex items-center gap-2">
                                    {{-- 🌟 ប្តូរទៅហៅ Function របស់ Livewire វិញពេលចុច --}}
                                    <button type="button" wire:click.stop="toggleExpanded({{ $item->id }})" class="w-6 h-6 flex items-center justify-center rounded hover:bg-[var(--color-text-muted)]/20 transition-all {{ $hasChildren ? 'text-[var(--color-primary)]' : 'opacity-0 cursor-default' }}" {{ !$hasChildren ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4 transition-transform duration-200 {{ $isExpanded ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                    {{ $item->name }}
                                    @if($hasChildren) <span class="px-2 py-0.5 bg-[var(--color-primary)]/10 text-[var(--color-primary)] text-[9px] rounded-full">{{ $children->count() }}</span> @endif
                                </div>
                            </td> 
                            @endif
                            
                            @if(in_array('url', $selectedColumns)) <td class="p-4 text-xs font-bold text-[var(--color-text-muted)]">{{ $item->url ?: '---' }}</td> @endif
                            
                            @if(in_array('icon', $selectedColumns)) 
                            <td class="p-4 text-xl text-[var(--color-text-main)]">
                                @if(!empty($item->icon) && Str::contains($item->icon, '<svg')) {!! $item->icon !!}
                                @elseif(!empty($item->icon)) <iconify-icon icon="{{ $item->icon }}"></iconify-icon>
                                @else <span class="text-xs opacity-50">---</span> @endif
                            </td> 
                            @endif
                            
                            @if(in_array('permission', $selectedColumns)) <td class="p-4 text-xs font-bold text-[var(--color-text-muted)]">{{ $item->permission ?: '---' }}</td> @endif
                            
                            @if(in_array('is_active', $selectedColumns))
                            <td class="p-4 text-center w-[1%]">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:click.prevent="toggleField({{ $item->id }}, 'is_active')" class="sr-only peer" {{ $item->is_active ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-[var(--color-primary)] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                                </label>
                            </td>
                            @endif

                            <td class="p-4 flex justify-end gap-1.5 items-center">
                                <button type="button" title="Drag to reorder" class="parent-drag-handle p-1.5 rounded-md text-[var(--color-text-muted)] hover:bg-[var(--color-text-muted)]/20 cursor-grab active:cursor-grabbing">
                                    <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                                </button>
                                <x-auth-button permission="edit-sidebar" wire:click.stop="editItem({{ $item->id }})" title="Edit" class="p-1.5 rounded-md text-blue-500 hover:bg-blue-500/10"><svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></x-auth-button>
                                <x-auth-button permission="delete-sidebar" wire:click.stop="confirmDelete({{ $item->id }})" title="Delete" class="p-1.5 rounded-md text-red-500 hover:bg-red-500/10"><svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></x-auth-button>
                            </td>
                        </tr>

                        @if($hasChildren && $isExpanded)
                        <tr class="bg-[var(--color-background)]/30 border-b border-[var(--color-border-color)]">
                            <td colspan="12" class="p-0">
                                <div class="pl-14 pr-4 py-3">
                                    <table class="w-full text-left border-l-2 border-[var(--color-primary)]/30">
                                        <tbody class="child-sortable divide-y divide-[var(--color-border-color)]/50">
                                            @foreach($children as $child)
                                                {{-- 🌟 បន្ថែម Hover Event នៅលើ Row កូន --}}
                                                <tr @mousedown="if(!$event.target.closest('button') && !$event.target.closest('a') && !$event.target.closest('label') && $event.target.tagName !== 'INPUT' && $event.target.tagName !== 'SVG' && $event.target.tagName !== 'PATH' && !$event.target.closest('.child-drag-handle')) { let cb = $el.querySelector('.row-checkbox'); if(cb && !cb.disabled) { checkStatus = !cb.checked; cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }" 
                                                    @mouseenter="if(isMouseDown && !$event.target.closest('button') && !$event.target.closest('a') && !$event.target.closest('label') && $event.target.tagName !== 'INPUT' && $event.target.tagName !== 'SVG' && $event.target.tagName !== 'PATH' && !$event.target.closest('.child-drag-handle')) { let cb = $el.querySelector('.row-checkbox'); if(cb && !cb.disabled && cb.checked !== checkStatus) { cb.checked = checkStatus; cb.dispatchEvent(new Event('change')); } }"
                                                    class="child-row bg-transparent hover:bg-[var(--color-card-bg)] transition-colors group select-none" data-id="{{ $child->id }}" wire:key="child-row-{{ $child->id }}-{{ time() }}">
                                                    
                                                    <td class="p-3 w-10 text-center"><input type="checkbox" wire:model.live="selectedItems" value="{{ $child->id }}" class="row-checkbox w-3.5 h-3.5 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] cursor-pointer"></td>
                                                    
                                                    @if(in_array('name', $selectedColumns)) 
                                                    <td class="p-3 text-xs font-bold text-[var(--color-text-main)] relative">
                                                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-px bg-[var(--color-primary)]/30"></span>
                                                        {{ $child->name }}
                                                    </td> 
                                                    @endif
                                                    
                                                    @if(in_array('url', $selectedColumns)) <td class="p-3 text-[11px] font-medium text-[var(--color-text-muted)]">{{ $child->url ?: '---' }}</td> @endif
                                                    
                                                    @if(in_array('icon', $selectedColumns)) 
                                                    <td class="p-3 text-lg text-[var(--color-text-muted)]">
                                                        @if(!empty($child->icon) && Str::contains($child->icon, '<svg')) {!! $child->icon !!}
                                                        @elseif(!empty($child->icon)) <iconify-icon icon="{{ $child->icon }}"></iconify-icon>
                                                        @else <span class="text-[10px] opacity-50">---</span> @endif
                                                    </td> 
                                                    @endif
                                                    
                                                    @if(in_array('permission', $selectedColumns)) <td class="p-3 text-[11px] font-medium text-[var(--color-text-muted)]">{{ $child->permission ?: '---' }}</td> @endif
                                                    
                                                    @if(in_array('is_active', $selectedColumns))
                                                    <td class="p-3 text-center w-[1%]">
                                                        <label class="relative inline-flex items-center cursor-pointer">
                                                            <input type="checkbox" wire:click.prevent="toggleField({{ $child->id }}, 'is_active')" class="sr-only peer" {{ $child->is_active ? 'checked' : '' }}>
                                                            <div class="w-9 h-5 bg-gray-300 dark:bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-[var(--color-primary)] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                                                        </label>
                                                    </td>
                                                    @endif

                                                    <td class="p-3 flex justify-end gap-1 items-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <button type="button" title="Drag to reorder child" class="child-drag-handle p-1 rounded text-[var(--color-text-muted)] hover:bg-[var(--color-text-muted)]/20 cursor-grab active:cursor-grabbing">
                                                            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                                                        </button>
                                                        <x-auth-button permission="edit-sidebar" wire:click.stop="editItem({{ $child->id }})" title="Edit" class="p-1 rounded text-blue-500 hover:bg-blue-500/10"><svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></x-auth-button>
                                                        <x-auth-button permission="delete-sidebar" wire:click.stop="confirmDelete({{ $child->id }})" title="Delete" class="p-1 rounded text-red-500 hover:bg-red-500/10"><svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></x-auth-button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                @endif
            @empty
                <tbody><tr><td colspan="10" class="p-16 text-center text-[var(--color-text-muted)] uppercase text-xs italic opacity-50">{{ __('messages.no_data') ?? 'No Data Found' }}</td></tr></tbody>
            @endforelse
        </table>
    </div>
    
   <div class="p-4 bg-[var(--color-background)]/30 border-t border-[var(--color-border-color)]">
        {{ $items->links('livewire.parts.pagination') }}
    </div>
</div>