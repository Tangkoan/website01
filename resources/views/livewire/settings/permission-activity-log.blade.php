<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-[var(--color-text-main)] tracking-tight flex items-center gap-3">
                <span class="p-2 bg-blue-500/10 rounded-lg md:rounded-xl text-blue-500 text-xl md:text-2xl">📋</span>
                {{ __('messages.activity_logs') ?? 'Activity Logs' }}
            </h2>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="/settings/permission" wire:navigate class="px-4 py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-black rounded-lg hover:brightness-95 transition-all text-sm flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('messages.back') ?? 'Back' }}
            </a>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="bg-[var(--color-card-bg)] p-3 rounded-xl border border-[var(--color-border-color)] shadow-sm">
        <div class="relative w-full lg:w-96 group">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[var(--color-text-muted)] pointer-events-none">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="searchTerm" class="w-full h-11 bg-[var(--color-background)] border border-transparent focus:border-[var(--color-primary)]/50 text-[var(--color-text-main)] rounded-lg pl-10 pr-4 text-sm font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 transition-all outline-none" placeholder="{{ __('messages.search') ?? 'Search...' }}">
        </div>
    </div>

    {{-- Desktop View --}}
    <div class="hidden md:block bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[var(--color-background)]/50 border-b border-[var(--color-border-color)]">
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.date_time') ?? 'Date & Time' }}</th>
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.causer') ?? 'Causer' }}</th>
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest text-center">{{ __('messages.event') ?? 'Event' }}</th>
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">{{ __('messages.details') ?? 'Details' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border-color)]">
                    @forelse($logs as $log)
                        <tr class="hover:bg-[var(--color-background)]/50 transition-all">
                            <td class="p-4">
                                <div class="text-sm font-bold text-[var(--color-text-main)]">{{ $log->created_at->format('d M, Y') }}</div>
                                <div class="text-[10px] font-black text-[var(--color-text-muted)] mt-0.5">{{ $log->created_at->format('h:i:s A') }}</div>
                            </td>
                            <td class="p-4 font-bold text-[var(--color-text-main)] text-sm">
                                {{ $log->causer ? $log->causer->name : 'System / Unknown' }}
                            </td>
                            <td class="p-4 text-center">
                                @php
                                    $colors = [
                                        'created' => 'bg-green-500/10 text-green-500 border-green-500/20',
                                        'updated' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                        'deleted' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                        'restored' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                    ];
                                    $colorClass = $colors[$log->event] ?? 'bg-gray-500/10 text-gray-500 border-gray-500/20';
                                @endphp
                                <span class="px-3 py-1 border rounded-full text-[10px] font-black uppercase tracking-widest {{ $colorClass }}">
                                    {{ $log->event }}
                                </span>
                            </td>
                            <td class="p-4 text-sm text-[var(--color-text-muted)]">
                                {{ $log->description }}
                                @if(isset($log->properties['attributes']['name']))
                                    <span class="font-bold text-[var(--color-text-main)]">"{{ $log->properties['attributes']['name'] }}"</span>
                                @elseif(isset($log->properties['old']['name']))
                                    <span class="font-bold text-[var(--color-text-main)]">"{{ $log->properties['old']['name'] }}"</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-16 text-center text-[var(--color-text-muted)] font-black uppercase text-xs tracking-widest italic opacity-50">{{ __('messages.no_data') ?? 'No Data' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 md:p-5 bg-[var(--color-background)]/30 border-t border-[var(--color-border-color)]">
            {{ $logs->links('livewire.parts.pagination') }}
        </div>
    </div>

    {{-- Mobile View (Card) --}}
    <div class="md:hidden space-y-3">
        @forelse($logs as $log)
            <div class="bg-[var(--color-card-bg)] p-4 rounded-xl shadow-sm border border-[var(--color-border-color)] flex flex-col gap-3">
                <div class="flex justify-between items-start">
                    <div class="flex flex-col">
                        <span class="font-bold text-[var(--color-text-main)] text-sm">{{ $log->causer ? $log->causer->name : 'System' }}</span>
                        <span class="text-[10px] font-black text-[var(--color-text-muted)] mt-0.5">{{ $log->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    @php
                        $colorClass = $colors[$log->event] ?? 'bg-gray-500/10 text-gray-500 border-gray-500/20';
                    @endphp
                    <span class="px-2 py-0.5 border rounded-md text-[9px] font-black uppercase tracking-widest {{ $colorClass }}">
                        {{ $log->event }}
                    </span>
                </div>
                
                <div class="border-t border-dashed border-[var(--color-border-color)]"></div>
                
                <div class="text-xs text-[var(--color-text-muted)] leading-relaxed">
                    {{ $log->description }}
                    @if(isset($log->properties['attributes']['name']))
                        <span class="font-bold text-[var(--color-text-main)]">"{{ $log->properties['attributes']['name'] }}"</span>
                    @elseif(isset($log->properties['old']['name']))
                        <span class="font-bold text-[var(--color-text-main)]">"{{ $log->properties['old']['name'] }}"</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-[var(--color-card-bg)] p-8 rounded-xl border border-[var(--color-border-color)] text-center text-[var(--color-text-muted)] font-black uppercase tracking-widest text-xs italic">{{ __('messages.no_data') ?? 'No Data' }}</div>
        @endforelse

        <div class="w-full flex justify-center mt-4">
            {{ $logs->links('livewire.parts.pagination') }}
        </div>
    </div>
</div>