<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Services\UserService;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserManagement extends Component
{
    use WithPagination;

    // ប្រើ $role វិញ ព្រោះរើសបានតែមួយ
    public $userId, $name, $email, $password, $status = true;
    public $role_id = '';
    
    public $isModalOpen = false, $searchTerm = '', $perPage = 10;
    public $sortField = 'id', $sortDirection = 'desc';
    
    public $availableColumns = ['name' => 'Name', 'email' => 'Email', 'role' => 'Role', 'status' => 'Status', 'created_at' => 'Created Date'];
    public $selectedColumns = ['name', 'email', 'role', 'status']; 
    public $selectedUsers = [], $selectAll = false;
    public $isDeleteModalOpen = false, $deleteId = null;

    // --- Variables សម្រាប់ Bulk Edit (បន្ថែមថ្មី) ---
    public $isBulkEditModalOpen = false;
    public $selectedItemsQueue = [];
    public $currentBulkIndex = 0;

    // Field សម្រាប់ Edit ក្នុង Bulk
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
        if (empty($this->selectedUsers)) return;

        // ទាញយក Users ដែលបាន Select យកមករៀបជាជួរ (Queue)
        $users = User::whereIn('id', $this->selectedUsers)->get();
        $this->selectedItemsQueue = $users->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'role_id' => $user->roles->first()?->id ?? ''
            ];
        })->toArray();

        $this->currentBulkIndex = 0;
        $this->loadBulkItemData($this->currentBulkIndex);
        $this->isBulkEditModalOpen = true;
    }

    private function loadBulkItemData($index)
    {
        if (!isset($this->selectedItemsQueue[$index])) return;
        
        $item = $this->selectedItemsQueue[$index];
        $this->bulkItemId = $item['id'];
        $this->bulkItemName = $item['name'];
        $this->bulkItemEmail = $item['email'];
        $this->bulkItemRole = $item['role_id'];
        $this->bulkItemStatus = (bool) $item['status'];
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
        // ធ្វើការ Validate មុននឹង Save
        $this->validate([
            'bulkItemName' => ['required', 'string', 'max:255'],
            'bulkItemEmail' => ['required', 'email', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($this->bulkItemId)],
            'bulkItemRole' => ['required']
        ]);

        // Save ទិន្នន័យទៅកាន់ Database តាមរយៈ Service
        $this->service()->saveUser([
            'name' => $this->bulkItemName,
            'email' => $this->bulkItemEmail,
            'status' => $this->bulkItemStatus,
            'role_id' => $this->bulkItemRole
        ], $this->bulkItemId);

        // Update ទិន្នន័យក្នុង Queue ដើម្បិអោយ UI Update តាម
        $this->selectedItemsQueue[$this->currentBulkIndex]['name'] = $this->bulkItemName;

        $this->moveToNextBulkItem();
    }

    private function moveToNextBulkItem()
    {
        $this->resetErrorBag();
        
        if ($this->currentBulkIndex < count($this->selectedItemsQueue) - 1) {
            $this->currentBulkIndex++;
            $this->loadBulkItemData($this->currentBulkIndex);
        } else {
            // បើកែដល់ទីបញ្ចប់ហើយ បិទ Modal
            $this->closeBulkEdit();
            $this->dispatch('notify', type: 'success', message: __('messages.bulk_edit_completed') ?? 'Bulk edit completed successfully.');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // ទាញយក ID របស់ User ទាំងអស់ដែលបង្ហាញលើ Table មកដាក់ចូលក្នុង array
            $myMaxLevel = auth()->user()->roles->max('level') ?? 0;
            $this->selectedUsers = $this->service()->getUsers($this->searchTerm, 'all', $this->sortField, $this->sortDirection, $myMaxLevel)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            // Clear ចោលវិញពេលដោះ tick
            $this->selectedUsers = [];
        }
    }

    public function closeBulkEdit()
    {
        $this->isBulkEditModalOpen = false;
        
        // ខ្ញុំបានបន្ថែម 'selectedUsers' និង 'selectAll' ចូលក្នុងនេះ ដើម្បី clear គ្រីសចេញ
        $this->reset([
            'selectedItemsQueue', 
            'currentBulkIndex', 
            'bulkItemId', 
            'bulkItemName', 
            'bulkItemEmail', 
            'bulkItemRole', 
            'bulkItemStatus', 
            'selectedUsers', // Clear ទិន្នន័យដែលបាន Select
            'selectAll'      // ដោះគ្រីស Select All
        ]);
        
        $this->resetErrorBag();
    }

    public function sortBy($field) {
        $this->sortDirection = ($this->sortField === $field && $this->sortDirection === 'asc') ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    public function openModal() {
        $this->reset(['userId', 'name', 'email', 'password', 'role_id']);
        $this->status = true;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function editUser($id) {
        $this->resetErrorBag();
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->status = (bool) $user->status;
        
        // ទាញយក Role ដំបូងគេរបស់ User មកបញ្ជាក់លើ Form
        $this->role_id = $user->roles->first()?->id ?? '';
        
        $this->isModalOpen = true;
    }

    public function toggleStatus($id) {
        $newStatus = $this->service()->toggleStatus($id);
        $this->dispatch('notify', type: 'success', message: $newStatus ? __('messages.user_activated') : __('messages.user_deactivated'));
    }

    public function saveUser() {
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
            'role_id' => $this->role_id // បញ្ជូន ID ទៅ Service
        ], $this->userId);

        $this->isModalOpen = false;
        $this->dispatch('notify', type: 'success', message: __('messages.user_saved'));
    }

    public function confirmDelete($id = null) {
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function executeDelete() {
        $ids = $this->deleteId ?: $this->selectedUsers;
        $this->service()->deleteUsers($ids);
        $this->reset(['selectedUsers', 'selectAll', 'deleteId', 'isDeleteModalOpen']);
        $this->dispatch('notify', type: 'success', message: __('messages.user_deleted'));
    }

    
    public function reloadData()
    {
        $this->reset(['searchTerm', 'selectedUsers', 'selectAll']);
        $this->resetPage(); // Reset Pagination ទៅទំព័រទី 1 វិញ
        $this->dispatch('notify', type: 'success', message: __('messages.data_reloaded') ?? 'Data reloaded successfully.');
    }

   public function render() {
        // ចាប់យក Level ធំបំផុតរបស់ Admin ដែលកំពុង Login (ឧទាហរណ៍៖ 99)
        $myMaxLevel = auth()->user()->roles->max('level') ?? 0; 
        
        // ទាញយក Roles ដែលមាន Level តូចជាង ឬស្មើខ្លួន (ដើម្បីកុំឱ្យ Admin អាចបង្កើត Super Admin បាន)
        $availableRoles = \App\Models\Role::whereNull('deleted_at')
                            ->where('level', '<=', $myMaxLevel)
                            ->get();

        return view('livewire.settings.user.user-management', [
            // បញ្ជូន $myMaxLevel ទៅឱ្យ Service ដើម្បីច្រោះទិន្នន័យ
            'users' => $this->service()->getUsers($this->searchTerm, $this->perPage, $this->sortField, $this->sortDirection, $myMaxLevel),
            'roles' => $availableRoles,
            'myMaxLevel' => $myMaxLevel
        ]);
    }
}