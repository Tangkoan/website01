<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Services\PermissionService;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class PermissionManagement extends Component
{
    use WithPagination;

    // --- States (រក្សាទុកដដែល) ---
    public $permissionId, $name, $guard_name = 'web';
    public $isModalOpen = false, $searchTerm = '', $perPage = 10;
    public $sortField = 'id', $sortDirection = 'desc';
    public $availableColumns = ['name' => 'Name', 'guard_name' => 'Guard', 'created_at' => 'Created Date'];
    public $selectedColumns = []; 
    public $selectedPermissions = [], $selectAll = false;
    public $isBulkEditModalOpen = false, $selectedItemsQueue = [], $currentBulkIndex = 0;
    public $bulkItemId, $bulkItemName, $bulkItemGuard;
    public $isDeleteModalOpen = false, $deleteId = null;

    protected $queryString = ['searchTerm', 'perPage'];

    // ហៅ Service មកប្រើ
    protected function service()
    {
        return app(PermissionService::class);
    }

    public function mount() {
        $savedColumns = session()->get('permission_columns', ['name', 'guard_name', 'created_at']);
        $this->selectedColumns = array_intersect($savedColumns, array_keys($this->availableColumns));
    }

    // --- UI Control Methods (Method ដែលបាត់ពីមុន) ---
    
    public function reloadData() {
        $this->reset(['searchTerm', 'selectedPermissions', 'selectAll']);
        $this->resetPage();
        $this->dispatch('notify', type: 'success', message: __('messages.data_reloaded'));
    }

    public function updatedSearchTerm() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function sortBy($field) {
        $this->sortDirection = ($this->sortField === $field && $this->sortDirection === 'asc') ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    public function updatedSelectedColumns($value) {
        session()->put('permission_columns', $value);
    }

    public function updatedSelectAll($value) {
        if ($value) {
            $this->selectedPermissions = collect($this->getPermissionsProperty()->items())
                ->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedPermissions = [];
        }
    }

    // --- Modal Control Methods (Method ដែលបាត់ពីមុន) ---

    public function openModal() {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function resetFields() {
        $this->reset(['permissionId', 'name', 'guard_name']);
        $this->resetErrorBag();
    }

    public function editPermission($id) {
        $this->resetFields();
        $p = \App\Models\Permission::findOrFail($id);
        $this->permissionId = $p->id;
        $this->name = $p->name;
        $this->guard_name = $p->guard_name;
        $this->isModalOpen = true;
    }

    public function confirmDelete($id = null) {
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    // --- Computed Property ---
    public function getPermissionsProperty() {
        return $this->service()->getPermissions(
            $this->searchTerm, 
            $this->perPage, 
            $this->sortField, 
            $this->sortDirection
        );
    }

    // --- Database Actions (ហៅប្រើ Service) ---

    public function savePermission() {
        $this->validate(['name' => ['required', Rule::unique('permissions', 'name')->ignore($this->permissionId)]]);

        $this->service()->savePermission([
            'name' => $this->name,
            'guard_name' => $this->guard_name
        ], $this->permissionId);

        $this->isModalOpen = false;
        $this->dispatch('notify', type: 'success', message: __('messages.permission_saved'));
    }

    public function executeDelete() {
        $ids = $this->deleteId ?: $this->selectedPermissions;
        $this->service()->deletePermissions($ids);

        $this->reset(['selectedPermissions', 'selectAll', 'deleteId', 'isDeleteModalOpen']);
        $this->dispatch('notify', type: 'success', message: __('messages.item_deleted'));
    }

    // --- Bulk Edit Logic ---

    public function bulkEdit() {
        if (empty($this->selectedPermissions)) return;
        
        $items = $this->service()->getPermissionsByIds($this->selectedPermissions);
        $this->selectedItemsQueue = $items->toArray();
        $this->currentBulkIndex = 0;
        $this->loadCurrentBulkItem();
        $this->isBulkEditModalOpen = true;
    }

    public function loadCurrentBulkItem() {
        if (!isset($this->selectedItemsQueue[$this->currentBulkIndex])) {
            $this->closeBulkEdit(); return;
        }
        $item = $this->selectedItemsQueue[$this->currentBulkIndex];
        $this->bulkItemId = $item['id'];
        $this->bulkItemName = $item['name'];
        $this->bulkItemGuard = $item['guard_name'];
    }

    public function jumpToBulkItem($index) {
        $this->currentBulkIndex = $index;
        $this->loadCurrentBulkItem();
    }

    public function saveAndNextBulkItem() {
        $this->validate(['bulkItemName' => ['required', Rule::unique('permissions', 'name')->ignore($this->bulkItemId)]]);
        
        $this->service()->savePermission([
            'name' => $this->bulkItemName,
            'guard_name' => $this->bulkItemGuard
        ], $this->bulkItemId);

        $this->dispatch('notify', type: 'success', message: __('messages.updated') . ' ' . $this->bulkItemName);
        $this->nextBulkItem();
    }

    public function skipBulkItem() {
        $this->nextBulkItem();
    }

    private function nextBulkItem() {
        $this->currentBulkIndex++;
        if ($this->currentBulkIndex >= count($this->selectedPermissions)) {
            $this->closeBulkEdit();
            $this->dispatch('notify', type: 'success', message: __('messages.bulk_edit_completed'));
        } else {
            $this->loadCurrentBulkItem();
        }
    }

    public function closeBulkEdit() {
        $this->isBulkEditModalOpen = false;
        $this->reset(['selectedPermissions', 'selectAll', 'selectedItemsQueue', 'currentBulkIndex']);
    }

    public function render() {
        return view('livewire.settings.permission.permission-management', [
            'permissions' => $this->getPermissionsProperty()
        ])->title(__('messages.permissions'));
    }
}