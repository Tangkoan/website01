<?php  // <--- សូមប្រាកដថាមានបន្ទាត់នេះនៅជួរទី១ ខាងលើគេបង្អស់!

if (!function_exists('get_sys_config')) {
    function get_sys_config($key, $default = null)
    {
        // ប្រើ Cache ដើម្បីឱ្យវាដើរលឿន មិនបាច់ Query រាល់ដង
        $configs = \Illuminate\Support\Facades\Cache::rememberForever('global_system_configs', function () {
            return \App\Models\SystemConfig::pluck('value', 'key')->toArray();
        });

        return $configs[$key] ?? $default;
    }
}