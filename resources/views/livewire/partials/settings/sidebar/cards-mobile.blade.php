<div class="md:hidden space-y-4">
    <div class="flex items-center gap-3 px-2 mb-2">
        <input type="checkbox" wire:model.live="selectAll" id="selectAllMobile-sidebar" class="w-5 h-5 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] bg-transparent cursor-pointer focus:ring-0">
        <label for="selectAllMobile-sidebar" class="text-sm font-black text-[var(--color-text-main)] uppercase tracking-widest">{{ __('messages.select_all') ?? 'Select All' }}</label>
    </div>

    @forelse($items as $item)
        @if(empty($item->parent_id))
            @php 
                $children = \App\Models\Sidebar::where('parent_id', $item->id)->orderBy('order', 'asc')->get(); 
                $hasChildren = $children->count() > 0;
            @endphp
            
            {{-- 🌟 ផ្តើម Card របស់មេ (មាន Alpine.js សម្រាប់ពន្លាតកូន) --}}
            <div wire:key="sidebar-mobile-parent-{{ $item->id }}-{{ time() }}" x-data="{ expanded: false }" class="bg-[var(--color-card-bg)] rounded-xl shadow-sm border border-[var(--color-border-color)] overflow-hidden flex flex-col transition-all">
                
                {{-- ផ្នែកខាងលើនៃ Card (Header) --}}
                <div class="p-4 flex flex-col gap-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" class="row-checkbox w-5 h-5 mt-1 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] bg-transparent cursor-pointer flex-shrink-0">
                            
                            <div class="min-w-0 flex-1 space-y-2">
                                {{-- ឈ្មោះ និង Icon (លេចធ្លោជាងគេ) --}}
                                <div class="flex items-center gap-2" @click="if(!$event.target.closest('button') && !$event.target.closest('a') && !$event.target.closest('label') && !$event.target.closest('input')) expanded = !expanded">
                                    @if(in_array('icon', $selectedColumns)) 
                                        <div class="w-8 h-8 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] flex items-center justify-center text-xl shrink-0">
                                            @if(!empty($item->icon) && Str::contains($item->icon, '<svg')) {!! $item->icon !!}
                                            @elseif(!empty($item->icon)) <iconify-icon icon="{{ $item->icon }}"></iconify-icon>
                                            @else <span class="text-[10px] font-bold">N/A</span> @endif
                                        </div>
                                    @endif
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-sm font-black text-[var(--color-text-main)] truncate uppercase tracking-wide cursor-pointer">{{ strip_tags($item->name) }}</span>
                                        @if(in_array('url', $selectedColumns)) <span class="text-[10px] font-bold text-[var(--color-text-muted)] truncate">{{ $item->url ?: '---' }}</span> @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 🌟 ជួសជុល Toggle Status: ប្រើ wire:change.stop ដើម្បីឱ្យ Animation រលូន --}}
                        @if(in_array('is_active', $selectedColumns))
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-1">
                            <input type="checkbox" wire:change.stop="toggleField({{ $item->id }}, 'is_active')" class="sr-only peer" {{ $item->is_active ? 'checked' : '' }}>
                            <div class="w-10 h-5 bg-gray-300 dark:bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-[var(--color-primary)] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                        </label>
                        @endif
                    </div>
                </div>
                
                {{-- ផ្នែកខាងក្រោមនៃ Card (Actions & Sub-menu Button) --}}
                <div class="px-4 py-3 bg-[var(--color-background)]/50 border-t border-[var(--color-border-color)] flex justify-between items-center">
                    {{-- ប៊ូតុងពន្លាតកូន --}}
                    <div>
                        @if($hasChildren)
                            <button type="button" @click="expanded = !expanded" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] text-xs font-bold transition-colors">
                                <span>{{ $children->count() }} {{ __('messages.items') ?? 'Items' }}</span>
                                <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7 7"></path></svg>
                            </button>
                        @else
                            <span class="text-[10px] text-[var(--color-text-muted)] font-bold italic uppercase">No Sub-menus</span>
                        @endif
                    </div>

                    {{-- ទីតាំង Action Buttons --}}
                    <div class="flex gap-2">
                        <x-auth-button permission="edit-sidebar" wire:click.stop="editItem({{ $item->id }})" class="p-2 rounded-lg bg-blue-500/10 text-blue-500 hover:bg-blue-500 hover:text-white transition-all disabled:opacity-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></x-auth-button>
                        <x-auth-button permission="delete-sidebar" wire:click.stop="confirmDelete({{ $item->id }})" class="p-2 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all disabled:opacity-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></x-auth-button>
                    </div>
                </div>

                {{-- 🌟 ផ្នែកកូនៗ (Child Cards) --}}
                @if($hasChildren)
                <div x-show="expanded" x-collapse x-cloak class="bg-[var(--color-card-bg)] border-t border-[var(--color-border-color)] p-3">
                    <div class="space-y-3 border-l-2 border-[var(--color-primary)]/30 ml-2 pl-3">
                        @foreach($children as $child)
                            <div class="bg-[var(--color-background)]/50 p-3 rounded-lg border border-[var(--color-border-color)] flex flex-col gap-2 relative">
                                <span class="absolute -left-3 top-5 w-3 h-px bg-[var(--color-primary)]/30"></span>
                                
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-start gap-2 min-w-0 flex-1">
                                        <input type="checkbox" wire:model.live="selectedItems" value="{{ $child->id }}" class="row-checkbox w-4 h-4 mt-0.5 rounded border-2 border-[var(--color-text-main)] text-[var(--color-primary)] bg-transparent cursor-pointer flex-shrink-0">
                                        
                                        <div class="flex flex-col min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                @if(in_array('icon', $selectedColumns)) 
                                                    <div class="text-sm text-[var(--color-text-muted)] flex items-center justify-center">
                                                        @if(!empty($child->icon) && Str::contains($child->icon, '<svg')) {!! $child->icon !!}
                                                        @elseif(!empty($child->icon)) <iconify-icon icon="{{ $child->icon }}"></iconify-icon>
                                                        @endif
                                                    </div>
                                                @endif
                                                <span class="text-xs font-bold text-[var(--color-text-main)] truncate">{{ strip_tags($child->name) }}</span>
                                            </div>
                                            @if(in_array('url', $selectedColumns)) <span class="text-[10px] font-medium text-[var(--color-text-muted)] truncate">{{ $child->url ?: '---' }}</span> @endif
                                        </div>
                                    </div>

                                    {{-- 🌟 ជួសជុល Toggle Status សម្រាប់កូន --}}
                                    @if(in_array('is_active', $selectedColumns))
                                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                        <input type="checkbox" wire:change.stop="toggleField({{ $child->id }}, 'is_active')" class="sr-only peer" {{ $child->is_active ? 'checked' : '' }}>
                                        <div class="w-8 h-4 bg-gray-300 dark:bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-[var(--color-primary)] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all"></div>
                                    </label>
                                    @endif
                                </div>
                                
                                <div class="flex justify-end gap-1.5 mt-1 border-t border-[var(--color-border-color)]/50 pt-2">
                                    <x-auth-button permission="edit-sidebar" wire:click.stop="editItem({{ $child->id }})" class="p-1.5 rounded bg-blue-500/10 text-blue-500 hover:bg-blue-500 hover:text-white transition-all disabled:opacity-50"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></x-auth-button>
                                    <x-auth-button permission="delete-sidebar" wire:click.stop="confirmDelete({{ $child->id }})" class="p-1.5 rounded bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all disabled:opacity-50"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></x-auth-button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        @endif
    @empty
        <div class="bg-[var(--color-card-bg)] p-8 rounded-xl border border-[var(--color-border-color)] text-center text-[var(--color-text-muted)] font-black uppercase tracking-widest text-xs italic">{{ __('messages.no_data') ?? 'No Data Found' }}</div>
    @endforelse

    <div class="pt-2">
        {{ $items->links('livewire.parts.pagination') }}
    </div>
</div>