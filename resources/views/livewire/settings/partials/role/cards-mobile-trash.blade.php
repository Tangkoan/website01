<div class="md:hidden space-y-3">
    @forelse($roles as $item)
        <div wire:key="mobile-trash-{{ $item->id }}" class="bg-[var(--color-card-bg)] p-4 rounded-xl shadow-sm border border-[var(--color-border-color)] flex flex-col gap-3">
            <div class="flex justify-between items-start">
                <div class="min-w-0">
                    <h4 class="font-black text-[var(--color-text-main)] text-base truncate">{{ $item->name }}</h4>
                    <span class="inline-block mt-1 px-2 py-0.5 bg-[var(--color-background)] border border-[var(--color-border-color)] rounded-md text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ $item->guard_name }}</span>
                </div>
                <div class="text-right">
                    <span class="block text-[9px] font-black text-red-500 uppercase tracking-widest">Deleted At</span>
                    <span class="text-[10px] font-bold text-[var(--color-text-muted)]">{{ $item->deleted_at->format('d M, Y H:i A') }}</span>
                </div>
            </div>
            
            <div class="border-t border-dashed border-[var(--color-border-color)] w-full"></div>
            
            <div class="flex justify-end gap-2">
                <button wire:click="restore({{ $item->id }})" class="flex-1 py-2 bg-green-50 text-green-600 font-black rounded-lg text-[10px] uppercase tracking-widest border border-green-200">
                    Restore
                </button>
                <button wire:click="confirmForceDelete({{ $item->id }})" class="flex-1 py-2 bg-red-50 text-red-600 font-black rounded-lg text-[10px] uppercase tracking-widest border border-red-200">
                    Delete
                </button>
            </div>
        </div>
    @empty
        <div class="bg-[var(--color-card-bg)] p-8 rounded-xl border border-[var(--color-border-color)] text-center text-[var(--color-text-muted)] font-black uppercase tracking-widest text-xs italic opacity-50">
            {{ __('messages.no_data') ?? 'No Data Found' }}
        </div>
    @endforelse

    <div class="w-full flex justify-center mt-4">
        {{ $roles->links('livewire.parts.pagination') }}
    </div>
</div>