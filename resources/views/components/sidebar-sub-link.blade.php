@props(['href', 'title'])
<a wire:navigate href="{{ $href }}" 
   {{ $attributes->merge(['class' => 'block px-4 py-2 text-sm transition-colors ' . (request()->is(trim($href, '/')) ? 'text-primary font-bold' : 'text-text-muted hover:text-primary')]) }}>
    {{ $title }}
</a>