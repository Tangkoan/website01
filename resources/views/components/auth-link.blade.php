@props([
    'permission', 
])

@php
    $hasPermission = auth()->user()->can($permission);
    $uiMode = Illuminate\Support\Facades\Cache::rememberForever('setting_role_ui_mode', function () {
        $setting = \App\Models\Setting::where('key', 'role_ui_mode')->first();
        return $setting ? $setting->value : 'hide';
    });
@endphp

@if($hasPermission)
    <a {{ $attributes }}>
        {{ $slot }}
    </a>
@elseif($uiMode === 'disable')
    <span {{ $attributes->merge(['class' => '!opacity-40 !grayscale !cursor-not-allowed !pointer-events-none']) }} title="គ្មានសិទ្ធិ (No Permission)">
        {{ $slot }}
    </span>
@endif