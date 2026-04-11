@props(['href', 'title', 'icon' => null])

@php
    // ដកសញ្ញា / ខាងមុខចេញបើមាន ដើម្បីឆែក Route ឱ្យត្រូវ
    $cleanHref = ltrim($href, '/');
    $isActive = request()->is($cleanHref . '*');
@endphp

<a wire:navigate href="{{ $href }}" class="flex items-center gap-3 py-2 px-3 rounded-lg transition-all duration-200 {{ $isActive ? 'text-primary bg-primary/5 font-bold' : 'text-text-muted hover:text-text-main hover:bg-background/50 font-medium' }}">
    
    {{-- 🌟 Icon សម្រាប់ Sub Menu --}}
    <span class="text-lg flex items-center justify-center {{ $isActive ? 'drop-shadow-sm' : 'opacity-80' }}">
        @if($icon && Str::contains($icon, '<svg')) 
            {!! $icon !!} 
        @else 
            <iconify-icon icon="{{ $icon ?? 'healthicons:alert-circle2x-outline' }}"></iconify-icon> 
        @endif
    </span>

    <span class="text-sm">{{ $title }}</span>
</a>