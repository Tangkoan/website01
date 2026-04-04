<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Services\UserService;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserManagement extends Component
{
    use WithPagination;

    public $userId, $name, $email, $password, $status = true;
    public $role_id = '';
    
    public $isModalOpen = false, $searchTerm = '', $perPage = 10;
    public $sortField = 'id', $sortDirection = 'desc';
    
    public $availableColumns = ['name' => 'Name', 'email' => 'Email', 'role' => 'Role', 'status' => 'Status', 'created_at' => 'Created Date'];
    public $selectedColumns = ['name', 'email', 'role', 'status']; 
    public $selectedUsers = [], $selectAll = false;
    public $isDeleteModalOpen = false, $deleteId = null;

    // --- Bulk Edit Variables ---
    public $isBulkEditModalOpen = false;
    public $selectedItemsQueue = []; // ✅ កែប្រែ៖ ផ្ទុកត្រឹមតែ Array នៃ ID ប៉ុណ្ណោះ (ឧ. [1, 5, 8])
    public $currentBulkIndex = 0;

    public $bulkItemId;
    public $bulkItemName;
    public $bulkItemEmail;
    public $bulkItemRole;
    public $bulkItemStatus;

    protected $queryString = ['searchTerm', 'perPage'];

    protected function service()
    {
        return app(UserService::class);
    }

    public function bulkEdit()
    {
        abort_if(Gate::denies('edit-user'), 403); // ✅ ថែម Security

        if (empty($this->selectedUsers)) return;

        // ✅ កែប្រែ៖ ទាញយកតែ ID ដាក់ចូល Queue ដើម្បីកុំឱ្យធ្ងន់ Payload
        $this->selectedItemsQueue = array_values($this->selectedUsers);
        $this->currentBulkIndex = 0;
        
        $this->loadBulkItemData($this->currentBulkIndex);
        $this->isBulkEditModalOpen = true;
    }

    private function loadBulkItemData($index)
    {
        if (!isset($this->selectedItemsQueue[$index])) return;
        
        // ✅ កែប្រែ៖ ទាញទិន្នន័យពី DB ភ្លាមៗពេលចុចដល់ (ចំណេញ Network)
        $userId = $this->selectedItemsQueue[$index];
        $user = User::with('roles')->find($userId);

        if ($user) {
            $this->bulkItemId = $user->id;
            $this->bulkItemName = $user->name;
            $this->bulkItemEmail = $user->email;
            $this->bulkItemRole = $user->roles->first()?->id ?? '';
            $this->bulkItemStatus = (bool) $user->status;
        }
    }

    public function jumpToBulkItem($index)
    {
        $this->currentBulkIndex = $index;
        $this->loadBulkItemData($index);
        $this->resetErrorBag();
    }

    public function skipBulkItem()
    {
        $this->moveToNextBulkItem();
    }

    public function saveAndNextBulkItem()
    {
        abort_if(Gate::denies('edit-user'), 403); // ✅ ថែម Security

        $this->validate([
            'bulkItemName' => ['required', 'string', 'max:255'],
            'bulkItemEmail' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->bulkItemId)],
            'bulkItemRole' => ['required']
        ]);

        $this->service()->saveUser([
            'name' => $this->bulkItemName,
            'email' => $this->bulkItemEmail,
            'status' => $this->bulkItemStatus,
            'role_id' => $this->bulkItemRole
        ], $this->bulkItemId);

        $this->moveToNextBulkItem();
    }

    private function moveToNextBulkItem()
    {
        $this->resetErrorBag();
        
        if ($this->currentBulkIndex < count($this->selectedItemsQueue) - 1) {
            $this->currentBulkIndex++;
            $this->loadBulkItemData($this->currentBulkIndex);
        } else {
            $this->closeBulkEdit();
            $this->dispatch('notify', type: 'success', message: __('messages.bulk_edit_completed') ?? 'Bulk edit completed successfully.');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $myMaxLevel = auth()->user()->roles->max('level') ?? 0;
            $this->selectedUsers = $this->service()->getUsers($this->searchTerm, 'all', $this->sortField, $this->sortDirection, $myMaxLevel)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function closeBulkEdit()
    {
        $this->isBulkEditModalOpen = false;
        $this->reset([
            'selectedItemsQueue', 'currentBulkIndex', 'bulkItemId', 
            'bulkItemName', 'bulkItemEmail', 'bulkItemRole', 
            'bulkItemStatus', 'selectedUsers', 'selectAll'
        ]);
        $this->resetErrorBag();
    }

    public function sortBy($field) {
        $this->sortDirection = ($this->sortField === $field && $this->sortDirection === 'asc') ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    public function openModal() {
        abort_if(Gate::denies('create-user'), 403); // ✅ ថែម Security

        $this->reset(['userId', 'name', 'email', 'password', 'role_id']);
        $this->status = true;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function editUser($id) {
        abort_if(Gate::denies('edit-user'), 403); // ✅ ថែម Security

        $this->resetErrorBag();
        $user = User::with('roles')->findOrFail($id);
        
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->status = (bool) $user->status;
        $this->role_id = $user->roles->first()?->id ?? '';
        
        $this->isModalOpen = true;
    }

    public function toggleStatus($id) 
    {
        // ✅ ជំនួសឱ្យការប្រើ abort_if ដែលចេញ Error អាក្រក់មើល យើងលោត Message ជូនដំណឹងវិញ
        if (Gate::denies('update-user-status')) {
            $this->dispatch('notify', type: 'error', message: __('messages.no_permission') ?? 'You do not have permission to update user status.');
            return; 
        }

        $targetUser = User::with('roles')->findOrFail($id);
        $targetMaxLevel = $targetUser->roles->max('level') ?? 0;
        $myMaxLevel = auth()->user()->roles->max('level') ?? 0;
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');
        
        // ការពារមិនឱ្យបិទខ្លួនឯង
        if ($targetUser->id === auth()->id()) {
            $this->dispatch('notify', type: 'warning', message: __('messages.cannot_change_self') ?? 'You cannot change your own status.');
            return; 
        }

        // ការពារកុំឱ្យ Admin តូច ទៅបិទ Admin ធំ
        if (!$isSuperAdmin && $targetMaxLevel >= $myMaxLevel) {
            $this->dispatch('notify', type: 'error', message: __('messages.restricted_level') ?? 'You cannot modify a user with a higher or equal role level.');
            return; 
        }

        // បើឆ្លងកាត់ការឆែកទាំងអស់ ទើបអនុញ្ញាតឱ្យ Save ចូល Database
        $newStatus = $this->service()->toggleStatus($id);
        $this->dispatch('notify', type: 'success', message: $newStatus ? (__('messages.user_activated') ?? 'User Activated') : (__('messages.user_deactivated') ?? 'User Deactivated'));
    }

    public function saveUser() {
        // ✅ ថែម Security មុន Save
        if ($this->userId) {
            abort_if(Gate::denies('edit-user'), 403);
        } else {
            abort_if(Gate::denies('create-user'), 403);
        }

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => $this->userId ? ['nullable', 'min:6'] : ['required', 'min:6'],
            'role_id' => ['required'] 
        ]);

        $this->service()->saveUser([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'status' => $this->status,
            'role_id' => $this->role_id
        ], $this->userId);

        $this->isModalOpen = false;
        $this->dispatch('notify', type: 'success', message: __('messages.user_saved'));
    }

    public function confirmDelete($id = null) {
        abort_if(Gate::denies('delete-user'), 403); // ✅ ថែម Security
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function executeDelete() {
        abort_if(Gate::denies('delete-user'), 403); // ✅ ថែម Security

        $ids = $this->deleteId ?: $this->selectedUsers;
        $this->service()->deleteUsers($ids);
        $this->reset(['selectedUsers', 'selectAll', 'deleteId', 'isDeleteModalOpen']);
        $this->dispatch('notify', type: 'success', message: __('messages.user_deleted'));
    }
    
    public function reloadData()
    {
        $this->reset(['searchTerm', 'selectedUsers', 'selectAll']);
        $this->resetPage(); 
        $this->dispatch('notify', type: 'success', message: __('messages.data_reloaded') ?? 'Data reloaded successfully.');
    }

   public function render() {
        $currentUser = auth()->user();
        $myMaxLevel = $currentUser->roles->max('level') ?? 0; 
        $isSuperAdmin = $currentUser->hasRole('Super Admin');
        
        $availableRoles = \App\Models\Role::whereNull('deleted_at')
                            ->where('level', '<=', $myMaxLevel)
                            ->get();

        return view('livewire.settings.user.user-management', [
            'users' => $this->service()->getUsers($this->searchTerm, $this->perPage, $this->sortField, $this->sortDirection, $myMaxLevel),
            'roles' => $availableRoles,
            'myMaxLevel' => $myMaxLevel,
            'isSuperAdmin' => $isSuperAdmin // ✅ បោះអថេរនេះទៅ Blade ដើម្បិកុំឱ្យ Blade ហៅ auth()->user() ច្រើនដង
        ])->title(__('messages.user_management'));
    }
}