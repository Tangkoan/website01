<?php
namespace App\Livewire\Settings;

use Livewire\Component;
use App\Services\RoleService;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class RoleManagement extends Component
{
    use WithPagination;

    public $roleId, $name, $guard_name = 'web', $level = 1; 
    public $isModalOpen = false, $searchTerm = '', $perPage = 10;
    public $sortField = 'id', $sortDirection = 'desc';
    public $availableColumns = ['name' => 'Name', 'guard_name' => 'Guard', 'level' => 'Level', 'created_at' => 'Created Date'];
    public $selectedColumns = []; 
    public $selectedRoles = [], $selectAll = false;
    public $isBulkEditModalOpen = false, $selectedItemsQueue = [], $currentBulkIndex = 0;
    public $bulkItemId, $bulkItemName, $bulkItemGuard, $bulkItemLevel; 
    public $isDeleteModalOpen = false, $deleteId = null;

    // Modal ទី១ (សិទ្ធិផ្ទាល់ខ្លួន)
    public $isPermissionModalOpen = false;
    public $managingRoleId = null;
    public $managingRoleName = '';
    public $rolePermissionsSelected = [];

    // Modal ទី២ (សិទ្ធិចែកចាយបន្ត)
    public $isAssignableModalOpen = false;
    public $roleAssignablePermissionsSelected = [];
    
    public $maxAllowedLevel;

    protected $queryString = ['searchTerm', 'perPage'];

    protected function service() { return app(RoleService::class); }

    public function mount() {
        $savedColumns = session()->get('role_columns', ['name', 'guard_name', 'level', 'created_at']);
        $this->selectedColumns = array_intersect($savedColumns, array_keys($this->availableColumns));
        $this->maxAllowedLevel = $this->service()->getMaxAllowedLevelForCurrentUser();
    }

    // [Helper Functions]
    public function reloadData() { $this->reset(['searchTerm', 'selectedRoles', 'selectAll']); $this->dispatch('notify', type: 'success', message: __('messages.data_reloaded')); }
    public function sortBy($field) { $this->sortDirection = ($this->sortField === $field && $this->sortDirection === 'asc') ? 'desc' : 'asc'; $this->sortField = $field; }
    public function updatedSelectAll($value) { $this->selectedRoles = $value ? collect($this->getRolesProperty()->items())->pluck('id')->map(fn($id) => (string)$id)->toArray() : []; }

    // ==========================================
    // Security Helper ជំនួសឱ្យ abort(403)
    // ==========================================
    protected function checkAccess($permission) {
        if (!Auth::user()->can($permission)) {
            $this->dispatch('notify', type: 'error', message: __('messages.403_description') ?? 'Access Denied: You do not have permission.');
            return false; // គ្មានសិទ្ធិ
        }
        return true; // មានសិទ្ធិ
    }

    // ==========================================
    // Core CRUD with Security
    // ==========================================
    public function openModal() {
        if (!$this->checkAccess('create_roles')) return; // បញ្ឈប់ដំណើរការប្រសិនបើគ្មានសិទ្ធិ
        
        $this->reset(['roleId', 'name', 'guard_name', 'level']);
        $this->isModalOpen = true;
    }

    public function editRole($id) {
        if (!$this->checkAccess('edit_roles')) return;

        $role = Role::findOrFail($id);
        if ($role->level > $this->maxAllowedLevel) {
            $this->dispatch('notify', type: 'error', message: __('messages.role_edit_unauthorized'));
            return;
        }
        $this->roleId = $role->id; $this->name = $role->name; $this->guard_name = $role->guard_name; $this->level = $role->level;
        $this->isModalOpen = true;
    }

    public function saveRole() {
        if ($this->roleId) { 
            if (!$this->checkAccess('edit_roles')) return;
        } else { 
            if (!$this->checkAccess('create_roles')) return;
        }

        $this->validate([
            'name' => ['required', Rule::unique('roles', 'name')->ignore($this->roleId)],
            'level' => ['required', 'integer', 'min:1', 'max:' . $this->maxAllowedLevel] 
        ], ['level.max' => __('messages.level_max_error', ['max' => $this->maxAllowedLevel])]);

        $this->service()->saveRole(['name' => $this->name, 'guard_name' => $this->guard_name, 'level' => $this->level], $this->roleId);
        $this->isModalOpen = false;
        $this->dispatch('notify', type: 'success', message: __('messages.role_saved'));
    }

    public function executeDelete() {
        if (!$this->checkAccess('delete_roles')) return;

        $ids = $this->deleteId ?: $this->selectedRoles;
        $this->service()->deleteRoles($ids);
        $this->reset(['selectedRoles', 'selectAll', 'deleteId', 'isDeleteModalOpen']);
        $this->dispatch('notify', type: 'success', message: __('messages.item_deleted'));
    }

    // ==========================================
    // Manage Permissions (Modal 1)
    // ==========================================
    public function managePermissions($id) {
        if (!$this->checkAccess('assign_permissions')) return;

        $role = Role::with('permissions')->findOrFail($id);
        $this->managingRoleId = $role->id; $this->managingRoleName = $role->name;
        $this->rolePermissionsSelected = $role->permissions->pluck('name')->toArray();
        $this->isPermissionModalOpen = true;
    }

    public function saveRolePermissions() {
        if (!$this->checkAccess('assign_permissions')) return;

        $role = Role::findOrFail($this->managingRoleId);
        $allowed = collect($this->service()->getAssignablePermissionsForCurrentUser())->pluck('name')->toArray();
        $valid = collect($this->rolePermissionsSelected)->intersect($allowed)->toArray();
        $role->syncPermissions($valid);
        $this->isPermissionModalOpen = false;
        $this->dispatch('notify', type: 'success', message: __('messages.permissions_updated'));
    }

    // ==========================================
    // Assignable Rules (Modal 2)
    // ==========================================
    public function manageAssignablePermissions($id) {
        if (!$this->checkAccess('assign_permissions')) return;

        $role = Role::with('assignablePermissions')->findOrFail($id);
        $this->managingRoleId = $role->id; $this->managingRoleName = $role->name;
        $this->roleAssignablePermissionsSelected = $role->assignablePermissions->pluck('name')->toArray();
        $this->isAssignableModalOpen = true;
    }

    public function saveAssignablePermissions() {
        if (!$this->checkAccess('assign_permissions')) return;

        $role = Role::findOrFail($this->managingRoleId);
        $allowed = collect($this->service()->getAssignablePermissionsForCurrentUser())->pluck('name')->toArray();
        $valid = collect($this->roleAssignablePermissionsSelected)->intersect($allowed)->toArray();
        $ids = \Spatie\Permission\Models\Permission::whereIn('name', $valid)->pluck('id')->toArray();
        $role->assignablePermissions()->sync($ids);
        $this->isAssignableModalOpen = false;
        $this->dispatch('notify', type: 'success', message: __('messages.assignable_permissions_updated'));
    }

    public function getRolesProperty() { return $this->service()->getRoles($this->searchTerm, $this->perPage, $this->sortField, $this->sortDirection); }

    public function render() {
        $allPermissions = collect($this->service()->getAssignablePermissionsForCurrentUser())
            ->whereNull('deleted_at');

        $groupedPermissions = $allPermissions->groupBy(function($perm) {
            $parts = explode('-', $perm->name);
            return count($parts) > 1 ? ucfirst($parts[count($parts) - 1]) : 'General';
        });

        return view('livewire.settings.role.role-management', [
            'roles' => $this->getRolesProperty(),
            'groupedPermissions' => $groupedPermissions 
        ]);
    }
}