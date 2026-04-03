<div class="w-full flex flex-col md:flex-row items-center justify-between gap-4 relative z-10">
    
    <div x-data="{ open: false, perPage: @entangle('perPage').live }" class="relative w-full md:w-auto">
        <div class="flex items-center justify-between md:justify-start gap-3 bg-[var(--color-card-bg)] px-4 py-2.5 rounded-xl md:rounded-2xl border border-[var(--color-border-color)] shadow-sm">
            <span class="text-[10px] md:text-xs font-black text-[var(--color-text-muted)] uppercase tracking-widest flex-shrink-0">{{ __('messages.per_page') ?? 'Per Page' }}:</span>
            <button @click="open = !open" @click.outside="open = false" type="button" class="flex items-center justify-between gap-2 text-sm font-black text-[var(--color-primary)] outline-none min-w-[3rem]">
                <span x-text="perPage === 'all' ? 'ALL' : perPage"></span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
        </div>

        <div x-show="open" x-transition.opacity.duration.200ms class="absolute bottom-full left-0 mb-2 w-full min-w-[6rem] bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-xl md:rounded-2xl shadow-[0_-10px_20px_rgba(0,0,0,0.1)] overflow-hidden z-[100] py-1 flex flex-col">
            @foreach([1,10, 20, 50, 100, 'all'] as $val)
                <button type="button" @click="perPage = '{{ $val }}'; open = false" class="w-full text-center px-2 py-2.5 text-sm font-bold transition-colors" :class="perPage == '{{ $val }}' ? 'text-[var(--color-primary)] bg-[var(--color-primary)]/10' : 'text-[var(--color-text-main)] hover:bg-[var(--color-background)]'">
                    {{ strtoupper($val) }}
                </button>
            @endforeach
        </div>
    </div>

    @if ($paginator->hasPages() && $paginator->count() > 0)
        <nav class="flex items-center justify-start md:justify-center gap-1.5 md:gap-2 w-full md:w-auto overflow-x-auto py-2 px-1 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:'none'] [scrollbar-width:none]">
            
            @if ($paginator->onFirstPage())
                <span class="p-2 md:p-3 text-[var(--color-text-muted)] opacity-30 cursor-not-allowed border border-[var(--color-border-color)] rounded-lg md:rounded-xl bg-[var(--color-background)] flex-shrink-0">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </span>
            @else
                <button wire:click="previousPage" class="p-2 md:p-3 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg md:rounded-xl text-[var(--color-text-main)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)] transition-all active:scale-90 shadow-sm flex-shrink-0">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
            @endif

            <div class="flex items-center gap-1.5 md:gap-2 flex-shrink-0">
                @foreach ($elements as $element)
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="w-8 h-8 md:w-11 md:h-11 flex items-center justify-center bg-[var(--color-primary)] text-white font-black text-xs md:text-sm rounded-lg md:rounded-xl shadow-lg shadow-[var(--color-primary)]/20 flex-shrink-0">{{ $page }}</span>
                            @else
                                <button wire:click="gotoPage({{ $page }})" class="w-8 h-8 md:w-11 md:h-11 flex items-center justify-center bg-[var(--color-card-bg)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-bold text-xs md:text-sm rounded-lg md:rounded-xl hover:border-[var(--color-primary)] hover:text-[var(--color-primary)] transition-all flex-shrink-0">{{ $page }}</button>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" class="p-2 md:p-3 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] rounded-lg md:rounded-xl text-[var(--color-text-main)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)] transition-all active:scale-90 shadow-sm flex-shrink-0">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            @else
                <span class="p-2 md:p-3 text-[var(--color-text-muted)] opacity-30 cursor-not-allowed border border-[var(--color-border-color)] rounded-lg md:rounded-xl bg-[var(--color-background)] flex-shrink-0">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            @endif
        </nav>
    @endif
</div>