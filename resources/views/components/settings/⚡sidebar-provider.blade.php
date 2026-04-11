<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    // 🌟 មុខងារនេះចាំស្តាប់ Event ពី File SidebarManagement.php (ពេលបង Add/Edit/Delete/Drag)
    #[On('refreshSidebar')]
    public function refreshSidebar()
    {
        // គ្រាន់តែប្រាប់ Livewire ឱ្យគូរ (Render) ផ្នែកនេះឡើងវិញ
    }
};
?>

<div>
    {{-- 🌟 ហៅ Component ធម្មតាមកប្រើនៅទីនេះ --}}
    <x-sidebar />
</div>