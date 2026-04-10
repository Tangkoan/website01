<?php

namespace App\Livewire\Settings;


use Livewire\Component;




use App\Models\Sidebar;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On; // ✅ ចាំបាច់

class SidebarProvider extends Component
{
    // មុខងារនេះនឹងរត់រាល់ពេលមានការបាញ់ Event ឈ្មោះ 'refreshSidebar'
    #[On('refreshSidebar')] 
    public function refreshMenu()
    {
        // លុប Cache ចាស់ចោល ដើម្បីឱ្យ render() ទាញទិន្នន័យថ្មីពី DB
        Cache::forget('sidebar_dynamic_menus');
    }

    public function render()
    {
        $dynamicMenus = Cache::rememberForever('sidebar_dynamic_menus', function () {
            return Sidebar::whereNull('parent_id')
                ->where('is_active', 1)
                ->with(['children' => fn($q) => $q->where('is_active', 1)->orderBy('order', 'asc')])
                ->orderBy('order', 'asc')
                ->get();
        });

        return view('components.sidebar', [
            'dynamicMenus' => $dynamicMenus
        ]);
    }
}