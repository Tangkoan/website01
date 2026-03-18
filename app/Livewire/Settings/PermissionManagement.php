<?php

namespace App\Livewire\Settings;

use Livewire\Component;
// use Spatie\Permission\Models\Permission;
use App\Models\Permission;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class PermissionManagement extends Component
{
    use WithPagination;

    // --- Single CRUD States ---
    public $permissionId, $name, $guard_name = 'web';
    public $isModalOpen = false;
    public $searchTerm = '', $perPage = 10;
    public $sortField = 'id', $sortDirection = 'desc';

    // --- Dynamic Columns States ---
    public $availableColumns = [
        'name' => 'Name',
        'guard_name' => 'Guard',
        'created_at' => 'Created Date'
    ];
    public $selectedColumns = []; 

    public function mount() {
        $savedColumns = session()->get('permission_columns', ['name', 'guard_name', 'created_at']);
        
        // តម្រង (Filter) យកតែឈ្មោះ Column ណាដែលមានពិតប្រាកដនៅក្នុងអថេរ $availableColumns
        $this->selectedColumns = array_intersect($savedColumns, array_keys($this->availableColumns));
    }

    public function updatedSelectedColumns($value) {
        // រក្សាទុកជម្រើសទៅក្នុង Session ពេល User ធីក ឬដោះធីក
        session()->put('permission_columns', $value);
    }
    
    // --- Bulk Edit States ---
    public $selectedPermissions = [];
    public $selectAll = false;

    public $isBulkEditModalOpen = false;
    public $selectedItemsQueue = []; 
    public $currentBulkIndex = 0;
    public $bulkItemId, $bulkItemName, $bulkItemGuard;

    public $isDeleteModalOpen = false;
    public $deleteId = null;

    protected $queryString = ['searchTerm', 'perPage'];
    
    public function updatedSearchTerm() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function reloadData() {
        $this->reset(['searchTerm', 'selectedPermissions', 'selectAll']);
        $this->resetPage();
        $this->dispatch('notify', type: 'success', message: __('messages.data_reloaded'));
    }

    public function sortBy($field) {
        $this->sortDirection = ($this->sortField === $field && $this->sortDirection === 'asc') ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    public function getPermissionsProperty() {
        $query = Permission::where('name', 'like', '%' . $this->searchTerm . '%')
            ->orderBy($this->sortField, $this->sortDirection);
        return ($this->perPage === 'all') ? $query->paginate(Permission::count()) : $query->paginate((int)$this->perPage);
    }

    public function updatedSelectAll($value) {
        if ($value) {
            $this->selectedPermissions = collect($this->getPermissionsProperty()->items())
                ->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedPermissions = [];
        }
    }

    // --- Single CRUD Logic ---
    public function openModal() { $this->resetFields(); $this->isModalOpen = true; }    

    public function resetFields() { 
        $this->reset(['permissionId', 'name', 'guard_name']); 
        $this->resetErrorBag(); 
    }

    public function editPermission($id) {
        $p = Permission::findOrFail($id);
        $this->permissionId = $p->id; $this->name = $p->name; $this->guard_name = $p->guard_name;
        $this->isModalOpen = true;
    }

    public function savePermission() {
        $this->validate(['name' => ['required', Rule::unique('permissions', 'name')->ignore($this->permissionId)]]);
        Permission::updateOrCreate(['id' => $this->permissionId], ['name' => $this->name, 'guard_name' => $this->guard_name]);
        $this->isModalOpen = false;
        $this->dispatch('notify', type: 'success', message: __('messages.permission_saved'));
    }

    public function confirmDelete($id = null) {
        $this->deleteId = $id; 
        $this->isDeleteModalOpen = true;
    }

    public function executeDelete() {
        if ($this->deleteId) {
            Permission::destroy($this->deleteId);
            $this->dispatch('notify', type: 'success', message: __('messages.item_deleted'));
        } else {
            Permission::whereIn('id', $this->selectedPermissions)->delete();
            $this->reset(['selectedPermissions', 'selectAll']);
            $this->dispatch('notify', type: 'success', message: __('messages.selected_deleted'));
        }
        $this->isDeleteModalOpen = false;
        $this->deleteId = null;
    }

    public function deleteSelected() {
        Permission::whereIn('id', $this->selectedPermissions)->delete();
        $this->reset(['selectedPermissions', 'selectAll']);
        $this->dispatch('notify', type: 'success', message: __('messages.selected_deleted'));
    }

    // --- Sequential Bulk Edit Logic ---
    public function bulkEdit() {
        if (empty($this->selectedPermissions)) return;
        
        $this->reset(['selectedItemsQueue', 'currentBulkIndex', 'bulkItemId', 'bulkItemName', 'bulkItemGuard']);
        $items = Permission::whereIn('id', $this->selectedPermissions)->get(['id', 'name', 'guard_name']);
        
        foreach($items as $item) {
            $this->selectedItemsQueue[] = ['id' => $item->id, 'name' => $item->name, 'guard_name' => $item->guard_name];
        }
        
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
        
        // action who delete
        $bulkPermission = Permission::find($this->bulkItemId);
        if ($bulkPermission) {
            $bulkPermission->update([
                'name' => $this->bulkItemName, 
                'guard_name' => $this->bulkItemGuard
            ]);
        }

        $this->selectedItemsQueue[$this->currentBulkIndex]['name'] = $this->bulkItemName;
        $this->selectedItemsQueue[$this->currentBulkIndex]['guard_name'] = $this->bulkItemGuard;
        
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
        return view('livewire.settings.permission-management', [
            'permissions' => $this->getPermissionsProperty()
        ]);
    }
}