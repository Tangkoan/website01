
@props(['permission' => null])

@php
    $hasPermission = auth()->user()->can($permission);
    $uiMode = Illuminate\Support\Facades\Cache::rememberForever('setting_role_ui_mode', function () {
        $setting = \App\Models\Setting::where('key', 'role_ui_mode')->first();
        return $setting ? $setting->value : 'hide';
    });
@endphp

@if($hasPermission)
    <button {{ $attributes }}>
        {{ $slot }}
    </button>
@elseif($uiMode === 'disable')
    <button disabled {{ $attributes->merge(['class' => '!opacity-40 !grayscale !cursor-not-allowed']) }} title="គ្មានសិទ្ធិ (No Permission)">
        {{ $slot }}
    </button>
@endif