<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Services\SidebarService;
use Livewire\WithPagination;

use App\Models\Sidebar;
use Illuminate\Support\Facades\Gate;

class SidebarManagement extends Component
{
    use WithPagination;
    

    public $itemId;
    
    // Single Form Auto-generated fields
    public $parent_id;
    public $name;
    public $url;
    public $icon;
    public $permission;
    public $order;
    public $is_active = true;
    
    // Bulk Form Auto-generated fields
    public $bulkItem_parent_id;
    public $bulkItem_name;
    public $bulkItem_url;
    public $bulkItem_icon;
    public $bulkItem_permission;
    public $bulkItem_order;
    public $bulkItem_is_active = true;
    
    public $isModalOpen = false, $searchTerm = '', $perPage = 10;
    public $sortField = 'id', $sortDirection = 'desc';
    
    public $availableColumns = ['parent_id' => 'Parent', 'name' => 'Name', 'url' => 'Url', 'icon' => 'Icon', 'permission' => 'Permission', 'order' => 'Order', 'is_active' => 'Is Active'];
    public $selectedColumns = ['parent_id', 'name', 'url', 'icon', 'permission', 'order', 'is_active']; 
    
    public $selectedItems = [], $selectAll = false;
    public $isDeleteModalOpen = false, $deleteId = null;

    public $isBulkEditModalOpen = false;
    public $selectedItemsQueue = []; 
    public $currentBulkIndex = 0;
    public $bulkItemId;

    protected $queryString = ['searchTerm', 'perPage'];

    protected function service() { return app(SidebarService::class); }

    public function updatedSelectAll($value) {
        if ($value) {
            $this->selectedItems = $this->service()->getItems($this->searchTerm, 'all', $this->sortField, $this->sortDirection)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function sortBy($field) {
        $this->sortDirection = ($this->sortField === $field && $this->sortDirection === 'asc') ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    // --- Bulk Edit ---
    public function bulkEdit() {
        abort_if(Gate::denies('edit-sidebar'), 403);
        if (empty($this->selectedItems)) return;
        $this->selectedItemsQueue = array_values($this->selectedItems);
        $this->currentBulkIndex = 0;
        $this->loadBulkItemData($this->currentBulkIndex);
        $this->isBulkEditModalOpen = true;
    }

    private function loadBulkItemData($index) {
        if (!isset($this->selectedItemsQueue[$index])) return;
        $item = Sidebar::find($this->selectedItemsQueue[$index]);
        if ($item) {
            $this->bulkItemId = $item->id;
            $this->bulkItem_parent_id = $item->parent_id;
            $this->bulkItem_name = $item->name;
            $this->bulkItem_url = $item->url;
            $this->bulkItem_icon = $item->icon;
            $this->bulkItem_permission = $item->permission;
            $this->bulkItem_order = $item->order;
            $this->bulkItem_is_active = (bool) $item->is_active;
        }
    }

    public function jumpToBulkItem($index) {
        $this->currentBulkIndex = $index;
        $this->loadBulkItemData($index);
        $this->resetErrorBag();
    }

    public function skipBulkItem() { $this->moveToNextBulkItem(); }

    public function saveAndNextBulkItem() {
        abort_if(Gate::denies('edit-sidebar'), 403);
        $this->validate([
            'bulkItem_parent_id' => 'required',
            'bulkItem_name' => 'required|string|max:255',
            'bulkItem_url' => 'required|string|max:255',
            'bulkItem_icon' => 'required|string|max:255',
            'bulkItem_permission' => 'required|string|max:255',
            'bulkItem_order' => 'required|string|max:255',
            'bulkItem_is_active' => 'nullable|boolean',
        ]);
        $this->service()->saveItem([
            'parent_id' => $this->bulkItem_parent_id,
            'name' => $this->bulkItem_name,
            'url' => $this->bulkItem_url,
            'icon' => $this->bulkItem_icon,
            'permission' => $this->bulkItem_permission,
            'order' => $this->bulkItem_order,
            'is_active' => $this->bulkItem_is_active,
        ], $this->bulkItemId);
        $this->moveToNextBulkItem();
    }

    private function moveToNextBulkItem() {
        $this->resetErrorBag();
        if ($this->currentBulkIndex < count($this->selectedItemsQueue) - 1) {
            $this->currentBulkIndex++;
            $this->loadBulkItemData($this->currentBulkIndex);
        } else {
            $this->closeBulkEdit();
            $this->dispatch('notify', type: 'success', message: __('messages.bulk_edit_completed') ?? 'Bulk edit completed.');
        }
    }

    public function closeBulkEdit() {
        $this->isBulkEditModalOpen = false;
        $this->reset(['selectedItemsQueue', 'currentBulkIndex', 'bulkItemId', 'selectedItems', 'selectAll', 'bulkItem_parent_id', 'bulkItem_name', 'bulkItem_url', 'bulkItem_icon', 'bulkItem_permission', 'bulkItem_order', 'bulkItem_is_active']);
        $this->resetErrorBag();
    }

    // --- Image Handling ---
    public function removeFile($field, $index) {
        if (is_array($this->$field) && isset($this->$field[$index])) {
            $files = $this->$field;
            unset($files[$index]);
            $this->$field = array_values($files); 
        }
    }

    // --- Single Actions ---
    public function openModal() {
        abort_if(Gate::denies('create-sidebar'), 403);
        $this->reset(['itemId']);
        
        $this->reset([
            'parent_id', 'name', 'url', 'icon', 'permission', 'order', 'is_active'
        ]);

        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function editItem($id) {
        abort_if(Gate::denies('edit-sidebar'), 403);
        $this->resetErrorBag();
        $item = Sidebar::findOrFail($id);
        
        $this->itemId = $item->id;
        
        $this->parent_id = $item->parent_id;
        $this->name = $item->name;
        $this->url = $item->url;
        $this->icon = $item->icon;
        $this->permission = $item->permission;
        $this->order = $item->order;
        $this->is_active = (bool) $item->is_active;
        
        $this->isModalOpen = true;
    }

    /**
     * ✅ កែប្រែទៅជា toggleField ដើម្បីឱ្យ Smart ជាងមុន
     * អាចបិទបើកបានគ្រប់ Field ដែលជាប្រភេទ Boolean
     */
    public function toggleField($id, $field) {
        if (Gate::denies('edit-sidebar')) {
            $this->dispatch('notify', type: 'error', message: __('messages.no_permission') ?? 'No permission.');
            return; 
        }
        $item = Sidebar::findOrFail($id);
        
        // ប្តូរតម្លៃ (Toggle logic)
        $item->$field = !$item->$field;
        $item->save();

        \Illuminate\Support\Facades\Cache::forget('sidebar_dynamic_menus');

        // ២. ✅ បាញ់ Event ទៅកាន់ SidebarProvider ឱ្យវា Re-render ភ្លាមៗ
        $this->dispatch('refreshSidebar')->to(SidebarProvider::class);
        
        $this->dispatch('notify', 
            type: 'success', 
            message: $item->$field ? (__('messages.activated') ?? 'Activated') : (__('messages.deactivated') ?? 'Deactivated')
        );
    }

    public function saveItem() {
        if ($this->itemId) abort_if(\Illuminate\Support\Facades\Gate::denies('edit-sidebar'), 403);
        else abort_if(\Illuminate\Support\Facades\Gate::denies('create-sidebar'), 403);

        $this->validate([
            'parent_id' => 'required',
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'permission' => 'required|string|max:255',
            'order' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $this->service()->saveItem([
                'parent_id' => $this->parent_id,
            'name' => $this->name,
            'url' => $this->url,
            'icon' => $this->icon,
            'permission' => $this->permission,
            'order' => $this->order,
            'is_active' => $this->is_active,
            ], $this->itemId);

            $this->isModalOpen = false;
            $this->dispatch('notify', type: 'success', message: __('messages.saved_successfully') ?? 'Data saved successfully.');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '22001' || \Illuminate\Support\Str::contains($e->getMessage(), '1406')) {
                
                preg_match("/column '([^']+)'/", $e->getMessage(), $matches);
                $columnName = $matches[1] ?? '';

                $fieldLabel = __("messages.$columnName");
                if ($fieldLabel == "messages.$columnName") {
                    $fieldLabel = \Illuminate\Support\Str::headline($columnName);
                }

                $this->dispatch('notify', 
                    type: 'error', 
                    message: __('messages.field_data_too_large', ['field' => $fieldLabel]) 
                             ?? "The data in field [$fieldLabel] is too large."
                );
                
                if ($columnName) {
                    $this->addError($columnName, __('messages.data_too_large'));
                }
                
                return;
            }

            throw $e;
        }
    }

    public function confirmDelete($id = null) {
        abort_if(Gate::denies('delete-sidebar'), 403);
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function executeDelete() {
        abort_if(Gate::denies('delete-sidebar'), 403);
        $ids = $this->deleteId ?: $this->selectedItems;
        $this->service()->deleteItems($ids);
        $this->reset(['selectedItems', 'selectAll', 'deleteId', 'isDeleteModalOpen']);
        \Illuminate\Support\Facades\Cache::forget('sidebar_dynamic_menus');
        $this->dispatch('notify', type: 'success', message: __('messages.deleted_successfully') ?? 'Deleted successfully.');
    }

    public function reloadData() {
        $this->reset(['searchTerm', 'selectedItems', 'selectAll']);
        $this->resetPage(); 
        $this->dispatch('notify', type: 'success', message: __('messages.data_reloaded') ?? 'Data reloaded.');
    }

    public function render() {
        return view('livewire.settings.sidebar.sidebar-management', [
            'items' => $this->service()->getItems($this->searchTerm, $this->perPage, $this->sortField, $this->sortDirection),
        ])->title(__('messages.sidebar_management') ?? 'Sidebars Management');
    }
}