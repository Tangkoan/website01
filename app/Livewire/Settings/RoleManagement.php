<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Services\RoleService;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleManagement extends Component
{
    use WithPagination;

    public $roleId, $name, $guard_name = 'web', $level = 1; // ថែម $level
    public $isModalOpen = false, $searchTerm = '', $perPage = 10;
    public $sortField = 'id', $sortDirection = 'desc';
    // ថែម 'level' ចូលក្នុង availableColumns
    public $availableColumns = ['name' => 'Name', 'guard_name' => 'Guard', 'level' => 'Level', 'created_at' => 'Created Date'];
    public $selectedColumns = []; 
    public $selectedRoles = [], $selectAll = false;
    public $isBulkEditModalOpen = false, $selectedItemsQueue = [], $currentBulkIndex = 0;
    public $bulkItemId, $bulkItemName, $bulkItemGuard, $bulkItemLevel; // ថែម $bulkItemLevel
    public $isDeleteModalOpen = false, $deleteId = null;

    public $isPermissionModalOpen = false;
    public $managingRoleId = null;
    public $managingRoleName = '';
    public $rolePermissionsSelected = [];
    public $maxAllowedLevel;

    protected $queryString = ['searchTerm', 'perPage'];

    protected function service()
    {
        return app(RoleService::class);
    }

    public function mount() {
        // ថែម 'level' ចូលក្នុង Columns លំនាំដើម
        $savedColumns = session()->get('role_columns', ['name', 'guard_name', 'level', 'created_at']);
        $this->selectedColumns = array_intersect($savedColumns, array_keys($this->availableColumns));
        $this->maxAllowedLevel = $this->service()->getMaxAllowedLevelForCurrentUser();
    }

    public function reloadData() {
        $this->reset(['searchTerm', 'selectedRoles', 'selectAll']);
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
        session()->put('role_columns', $value);
    }

    public function updatedSelectAll($value) {
        if ($value) {
            $this->selectedRoles = collect($this->getRolesProperty()->items())
                ->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedRoles = [];
        }
    }

    public function openModal() {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function resetFields() {
        $this->reset(['roleId', 'name', 'guard_name', 'level']); // ថែម 'level' ឱ្យ reset ពេលបើក Modal ថ្មី
        $this->resetErrorBag();
    }

    public function editRole($id) {
        $this->resetFields();
        $role = collect($this->getRolesProperty()->items())->firstWhere('id', $id) ?? Role::findOrFail($id);
        
        // --- បន្ថែមការការពារលើមុខងារRole Level ធំ ---
        if ($role->level > $this->maxAllowedLevel) {
            $this->dispatch('notify', type: 'error', message: __('messages.role_edit_unauthorized'));
            return; // បញ្ឈប់ដំណើរការ
        }
        // ------------------------

        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->guard_name = $role->guard_name;
        $this->level = $role->level ?? 1;
        $this->isModalOpen = true;
    }

    public function getRolesProperty() {
        return $this->service()->getRoles($this->searchTerm, $this->perPage, $this->sortField, $this->sortDirection);
    }

    public function saveRole() {
        // Validation កំណត់ឱ្យ level ត្រូវតែជាលេខ និងមិនធំជាង maxAllowedLevel
        $this->validate([
            'name' => ['required', Rule::unique('roles', 'name')->ignore($this->roleId)],
            'level' => ['required', 'integer', 'min:1', 'max:' . $this->maxAllowedLevel] // បន្ថែម max
        ], [
            'level.max' => __('messages.level_max_error', ['max' => $this->maxAllowedLevel]) ?? "You cannot set a level higher than your own ({$this->maxAllowedLevel})."
        ]);

        $this->service()->saveRole([
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'level' => $this->level
        ], $this->roleId);

        $this->isModalOpen = false;
        $this->dispatch('notify', type: 'success', message: __('messages.role_saved'));
    }

    public function managePermissions($id) {
        $this->resetErrorBag();
        $role = Role::with('permissions')->findOrFail($id);
        $this->managingRoleId = $role->id;
        $this->managingRoleName = $role->name;
        $this->rolePermissionsSelected = $role->permissions->pluck('name')->toArray();
        $this->isPermissionModalOpen = true;
    }

    public function saveRolePermissions() {
        $role = Role::findOrFail($this->managingRoleId);
        
        $activePermissions = collect($this->service()->getAllPermissions())
            ->whereNull('deleted_at')
            ->pluck('name')
            ->toArray();

        $validPermissions = collect($this->rolePermissionsSelected)
            ->intersect($activePermissions)
            ->toArray();

        $role->syncPermissions($validPermissions);
        
        $this->isPermissionModalOpen = false;
        
        $this->dispatch('notify', type: 'success', message: __('messages.permissions_updated'));
    }

    public function confirmDelete($id = null) {
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function executeDelete() {
        $ids = $this->deleteId ?: $this->selectedRoles;
        $this->service()->deleteRoles($ids);
        $this->reset(['selectedRoles', 'selectAll', 'deleteId', 'isDeleteModalOpen']);
        $this->dispatch('notify', type: 'success', message: __('messages.item_deleted'));
    }

    public function bulkEdit() {
        if (empty($this->selectedRoles)) return;
        $items = $this->service()->getRolesByIds($this->selectedRoles);
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
        $this->bulkItemLevel = $item['level'] ?? 1; // ទាញយក level សម្រាប់ Bulk Edit
    }

    public function saveAndNextBulkItem() {
        // Validation កំណត់ឱ្យ level ត្រូវតែជាលេខ និងមិនធំជាង maxAllowedLevel សម្រាប់ Bulk Edit ដែរ
        $this->validate([
            'bulkItemName' => ['required', Rule::unique('roles', 'name')->ignore($this->bulkItemId)],
            'bulkItemLevel' => ['required', 'integer', 'min:1', 'max:' . $this->maxAllowedLevel] // បន្ថែម max
        ], [
            'bulkItemLevel.max' => __('messages.level_max_error', ['max' => $this->maxAllowedLevel]) ?? "You cannot set a level higher than your own ({$this->maxAllowedLevel})."
        ]);
        
        $this->service()->saveRole([
            'name' => $this->bulkItemName,
            'guard_name' => $this->bulkItemGuard,
            'level' => $this->bulkItemLevel
        ], $this->bulkItemId);
        $this->nextBulkItem();
    }

    private function nextBulkItem() {
        $this->currentBulkIndex++;
        if ($this->currentBulkIndex >= count($this->selectedRoles)) {
            $this->closeBulkEdit();
        } else {
            $this->loadCurrentBulkItem();
        }
    }

    public function closeBulkEdit() {
        $this->isBulkEditModalOpen = false;
        $this->reset(['selectedRoles', 'selectAll', 'selectedItemsQueue', 'currentBulkIndex']);
    }

    public function render() {
        $allPermissions = collect($this->service()->getAllPermissions())
            ->whereNull('deleted_at')
            ->all();

        $groupedPermissions = collect($allPermissions)->groupBy(function($perm) {
            $parts = explode('-', $perm->name);
            return count($parts) > 1 ? ucfirst(end($parts)) : 'General';
        });

        return view('livewire.settings.role.role-management', [
            'roles' => $this->getRolesProperty(),
            'groupedPermissions' => $groupedPermissions 
        ]);
    }

    public function getAllPermissions() {
        return \Spatie\Permission\Models\Permission::all(); 
    }

    public function selectAllPermissions() {
        $this->rolePermissionsSelected = collect($this->service()->getAllPermissions())
            ->pluck('name')
            ->toArray();
    }

    public function unselectAllPermissions() {
        $this->rolePermissionsSelected = [];
    }
}