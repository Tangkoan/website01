<div class="md:hidden space-y-3">
    @forelse($users as $user)
        <div wire:key="mobile-trash-{{ $user->id }}" class="bg-[var(--color-card-bg)] p-4 rounded-xl shadow-sm border border-[var(--color-border-color)] flex flex-col gap-3">
            <div class="flex justify-between items-start">
                <div class="min-w-0 flex items-center gap-3">
                    <div class="h-10 w-10 flex-shrink-0">
                        <span class="h-10 w-10 rounded-full bg-[var(--color-primary)]/10 flex items-center justify-center text-[var(--color-primary)] font-black border border-[var(--color-primary)]/20">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-black text-[var(--color-text-main)] text-base truncate">{{ $user->name }}</h4>
                        <span class="inline-block mt-1 text-[10px] font-bold text-[var(--color-text-muted)] truncate">{{ $user->email }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="block text-[9px] font-black text-red-500 uppercase tracking-widest">Deleted At</span>
                    <span class="text-[10px] font-bold text-[var(--color-text-muted)]">{{ $user->deleted_at->format('d M, Y H:i A') }}</span>
                </div>
            </div>
            
            <div class="border-t border-dashed border-[var(--color-border-color)] w-full"></div>
            
            <div class="flex justify-end gap-2">
                <button wire:click="restore({{ $user->id }})" class="flex-1 py-2 bg-green-50 text-green-600 font-black rounded-lg text-[10px] uppercase tracking-widest border border-green-200">
                    Restore
                </button>
                <button wire:click="confirmForceDelete({{ $user->id }})" class="flex-1 py-2 bg-red-50 text-red-600 font-black rounded-lg text-[10px] uppercase tracking-widest border border-red-200">
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
        {{ $users->links('livewire.parts.pagination') }}
    </div>
</div>