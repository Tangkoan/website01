@props([
    'isOpen' => false, 
    'onClose', 
    'onConfirm', 
    'title' => __('messages.confirm_delete') ?? 'Are you sure?', 
    'message' => 'This action cannot be undone.'
])

@if($isOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-xl bg-black/40 dark:bg-black/60 animate-in fade-in duration-300">
        <div class="bg-[var(--color-card-bg)] w-full max-w-sm rounded-xl shadow-2xl border border-[var(--color-border-color)] overflow-hidden text-center p-6">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <h3 class="text-xl font-black text-[var(--color-text-main)] mb-2">{{ $title }}</h3>
            <p class="text-xs font-bold text-[var(--color-text-muted)] mb-6 leading-relaxed">
                {!! $message !!}
            </p>
            <div class="flex gap-3">
                <button wire:click="{{ $onClose }}" class="flex-1 h-10 bg-[var(--color-background)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-black rounded-lg hover:brightness-95 dark:hover:brightness-110 transition-all uppercase tracking-widest text-[10px] shadow-sm">
                    {{ __('messages.cancel') ?? 'Cancel' }}
                </button>
                <button wire:click="{{ $onConfirm }}" class="flex-1 h-10 bg-red-500 text-white font-black rounded-lg shadow-xl shadow-red-500/20 hover:bg-red-600 active:scale-95 transition-all uppercase tracking-widest text-[10px]">
                    {{ __('messages.delete') ?? 'Delete' }}
                </button>
            </div>
        </div>
    </div>
@endif