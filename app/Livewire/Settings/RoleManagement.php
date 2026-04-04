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
        // 💡 កែត្រង់នេះ៖ បន្ថែមការហៅ closeDeleteModal()
        $this->closeDeleteModal(); 
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


    // ==========================================
    // Modal Controllers (មុខងារបើក/បិទ Modal)
    // ==========================================

    // សម្រាប់លុប ១ ជួរ (Single Delete)
    public function confirmDelete($id)
    {
        if (!$this->checkAccess('delete_roles')) return;

        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    // សម្រាប់លុបច្រើនជួរ (Bulk Delete)
    public function confirmBulkDelete()
    {
        if (!$this->checkAccess('delete_roles')) return;

        if (empty($this->selectedRoles)) {
            $this->dispatch('notify', type: 'error', message: __('messages.no_items_selected') ?? 'Please select at least one item.');
            return;
        }

        $this->deleteId = null; // null មានន័យថាលុបតាម Array $selectedRoles
        $this->isDeleteModalOpen = true;
    }

    // សម្រាប់បិទ Modal លុប
    public function closeDeleteModal()
    {
        $this->isDeleteModalOpen = false;
        $this->deleteId = null;
    }


    // ==========================================
    // Bulk Edit (កែប្រែច្រើនជួរ)
    // ==========================================

    // ពេលចុចប៊ូតុង Bulk Edit ធំ
    public function bulkEdit()
    {
        if (!$this->checkAccess('edit_roles')) return;

        if (empty($this->selectedRoles)) {
            $this->dispatch('notify', type: 'error', message: __('messages.no_items_selected') ?? 'Please select at least one item.');
            return;
        }

        // ទាញយក Role ទាំងនោះពី Database
        $this->selectedItemsQueue = $this->service()->getRolesByIds($this->selectedRoles)->toArray();
        $this->currentBulkIndex = 0;

        if (count($this->selectedItemsQueue) > 0) {
            $this->loadBulkItemData();
            $this->isBulkEditModalOpen = true;
        }
    }

    // ទាញយកទិន្នន័យដាក់ចូលប្រអប់ Input សម្រាប់ Item នីមួយៗ
    private function loadBulkItemData()
    {
        $item = $this->selectedItemsQueue[$this->currentBulkIndex];
        $this->bulkItemId = $item['id'];
        $this->bulkItemName = $item['name'];
        $this->bulkItemGuard = $item['guard_name'];
        $this->bulkItemLevel = $item['level'];
    }

    // ពេលចុច Save Next ក្នុង Bulk Edit Modal
    public function saveBulkEditNext()
    {
        if (!$this->checkAccess('edit_roles')) return;

        $this->validate([
            'bulkItemName' => ['required', Rule::unique('roles', 'name')->ignore($this->bulkItemId)],
            'bulkItemLevel' => ['required', 'integer', 'min:1', 'max:' . $this->maxAllowedLevel] 
        ], ['bulkItemLevel.max' => __('messages.level_max_error', ['max' => $this->maxAllowedLevel])]);

        // Save ទិន្នន័យ
        $this->service()->saveRole([
            'name' => $this->bulkItemName, 
            'guard_name' => $this->bulkItemGuard, 
            'level' => $this->bulkItemLevel
        ], $this->bulkItemId);

        // ប្តូរទៅកាន់ Item បន្ទាប់
        $this->currentBulkIndex++;

        // បើអស់ហើយ បិទ Modal
        if ($this->currentBulkIndex >= count($this->selectedItemsQueue)) {
            $this->isBulkEditModalOpen = false;
            $this->reset(['selectedRoles', 'selectAll', 'selectedItemsQueue']);
            $this->dispatch('notify', type: 'success', message: __('messages.bulk_edit_completed') ?? 'All selected items updated successfully!');
        } else {
            // បើមិនទាន់អស់ Load Item បន្ទាប់
            $this->loadBulkItemData();
        }
    }

    // សម្រាប់បិទ Modal Bulk Edit កណ្តាលទី
    public function closeBulkEdit()
    {
        $this->isBulkEditModalOpen = false;
        $this->selectedItemsQueue = [];
        $this->reset(['bulkItemId', 'bulkItemName', 'bulkItemGuard', 'bulkItemLevel']);
    }

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
        ])->title(__('messages.role'));
    }
}