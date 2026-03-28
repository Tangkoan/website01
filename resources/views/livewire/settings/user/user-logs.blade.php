<div class="w-full max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6 relative">
    
    {{-- ១. Header (Title & Back Button) --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-[var(--color-text-main)] tracking-tight flex items-center gap-3">
                <span class="p-2 bg-blue-500/10 rounded-lg md:rounded-xl text-blue-500 text-xl md:text-2xl">📋</span>
                {{ __('messages.user_activity_logs') ?? 'User Activity Logs' }}
            </h2>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <a href="{{ route('settings.users') }}" wire:navigate class="px-4 py-2.5 bg-[var(--color-card-bg)] border border-[var(--color-border-color)] text-[var(--color-text-main)] font-black rounded-lg hover:brightness-95 transition-all text-sm flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('messages.back') ?? 'Back' }}
            </a>
        </div>
    </div>

    {{-- ២. Filters (Search) --}}
    <div class="bg-[var(--color-card-bg)] p-3 rounded-xl border border-[var(--color-border-color)] shadow-sm">
        <div class="relative w-full lg:w-96 group">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[var(--color-text-muted)] group-focus-within:text-[var(--color-primary)]">
                <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                class="w-full h-10 md:h-11 bg-[var(--color-background)] border-transparent text-[var(--color-text-main)] rounded-lg pl-10 pr-4 text-sm font-bold focus:ring-4 focus:ring-[var(--color-primary)]/10 outline-none" 
                placeholder="{{ __('messages.search_logs') ?? 'Search logs...' }}">
        </div>
    </div>

    <div class="space-y-4">
        {{-- ៣. Desktop Table (បង្ហាញតែលើ Screen ធំ) --}}
        <div class="hidden md:block bg-[var(--color-card-bg)] rounded-xl border border-[var(--color-border-color)] shadow-sm min-h-[300px] relative overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[var(--color-background)]/50 border-b border-[var(--color-border-color)]">
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest w-1/4">{{ __('messages.date_and_time') ?? 'Date & Time' }}</th>
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest w-1/4">{{ __('messages.admin_causer') ?? 'Causer (Admin)' }}</th>
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest w-1/6">{{ __('messages.action') ?? 'Action' }}</th>
                        <th class="p-4 text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest w-1/3">{{ __('messages.details') ?? 'Details' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border-color)]">
                    @forelse($logs as $log)
                        <tr class="hover:bg-[var(--color-background)]/50 transition-all select-none text-sm">
                            <td class="p-4 font-black text-[var(--color-text-main)]">
                                <div>{{ $log->created_at->format('d M, Y') }}</div>
                                <div class="text-[10px] font-bold text-[var(--color-text-muted)] uppercase tracking-widest">{{ $log->created_at->format('H:i:s A') }}</div>
                            </td>
                            <td class="p-4 font-black text-[var(--color-primary)]">
                                {{ $log->causer?->name ?? 'System' }}
                            </td>
                            <td class="p-4">
                                @php
                                    $action = strtolower($log->description);
                                    $bgColor = 'bg-gray-100 text-gray-700';
                                    if ($action === 'created') $bgColor = 'bg-green-100 text-green-700';
                                    if ($action === 'updated') $bgColor = 'bg-amber-100 text-amber-700';
                                    if ($action === 'deleted') $bgColor = 'bg-red-100 text-red-700';
                                    if ($action === 'restored') $bgColor = 'bg-blue-100 text-blue-700';
                                @endphp
                                <span class="px-3 py-1.5 rounded text-[10px] font-black uppercase tracking-widest {{ $bgColor }}">
                                    {{ $action }}
                                </span>
                            </td>
                            <td class="p-4 font-bold text-[var(--color-text-main)]">
                                @php
                                    $userName = $log->subject?->name 
                                                ?? $log->properties['attributes']['name'] 
                                                ?? $log->properties['old']['name'] 
                                                ?? 'Unknown User';
                                @endphp
                                {{ __('messages.this_user_has_been') ?? 'This user account has been' }} 
                                {{ $action }} 
                                <span class="text-[var(--color-primary)]">"{{ $userName }}"</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-16 text-center text-[var(--color-text-muted)] uppercase text-xs italic opacity-50">{{ __('messages.no_activity_found') ?? 'No activity found' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ៤. Mobile Card View (បង្ហាញតែលើ Screen តូច) --}}
        <div class="md:hidden space-y-3">
            @forelse($logs as $log)
                <div class="bg-[var(--color-card-bg)] p-4 rounded-xl shadow-sm border border-[var(--color-border-color)] flex flex-col gap-3">
                    <div class="flex justify-between items-start">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">Date & Time</span>
                            <span class="font-black text-[var(--color-text-main)] text-sm">{{ $log->created_at->format('d M, Y') }}</span>
                            <span class="text-[10px] font-bold text-[var(--color-text-muted)] uppercase tracking-widest">{{ $log->created_at->format('H:i:s A') }}</span>
                        </div>
                        @php
                            $action = strtolower($log->description);
                            $bgColor = 'bg-gray-100 text-gray-700';
                            if ($action === 'created') $bgColor = 'bg-green-100 text-green-700';
                            if ($action === 'updated') $bgColor = 'bg-amber-100 text-amber-700';
                            if ($action === 'deleted') $bgColor = 'bg-red-100 text-red-700';
                            if ($action === 'restored') $bgColor = 'bg-blue-100 text-blue-700';
                        @endphp
                        <span class="px-2.5 py-1 rounded text-[9px] font-black uppercase tracking-widest {{ $bgColor }}">
                            {{ $action }}
                        </span>
                    </div>

                    <div class="border-t border-dashed border-[var(--color-border-color)]"></div>

                    <div>
                        <span class="block text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest">Causer (Admin)</span>
                        <span class="text-xs font-black text-[var(--color-primary)]">{{ $log->causer?->name ?? 'System' }}</span>
                    </div>

                    <div class="bg-[var(--color-background)]/50 p-3 rounded-lg border border-[var(--color-border-color)]">
                        <span class="block text-[9px] font-black text-[var(--color-text-muted)] uppercase tracking-widest mb-1">Details</span>
                        @php
                            $userName = $log->subject?->name 
                                        ?? $log->properties['attributes']['name'] 
                                        ?? $log->properties['old']['name'] 
                                        ?? 'Unknown User';
                        @endphp
                        <p class="text-xs font-bold text-[var(--color-text-main)] leading-relaxed">
                            {{ __('messages.this_user_has_been') ?? 'This user account has been' }} 
                            {{ $action }} 
                            <span class="text-[var(--color-primary)]">"{{ $userName }}"</span>
                        </p>
                    </div>
                </div>
            @empty
                <div class="bg-[var(--color-card-bg)] p-8 rounded-xl border border-[var(--color-border-color)] text-center text-[var(--color-text-muted)] font-black uppercase tracking-widest text-xs italic opacity-50">
                    {{ __('messages.no_activity_found') ?? 'No activity found' }}
                </div>
            @endforelse
        </div>
    </div>

    {{-- ៥. Pagination --}}
    <div class="mt-4">
        {{ $logs->links('livewire.parts.pagination') }}
    </div>
    
</div>