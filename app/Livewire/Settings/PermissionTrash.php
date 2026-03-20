<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Permission; // ប្រើ Custom Model ដែលយើងបានបង្កើត

class PermissionTrash extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $perPage = 10;
    
    // --- Bulk Action States ---
    public $selectedPermissions = [];
    public $selectAll = false;

    // --- Delete Modal States ---
    public $isDeleteModalOpen = false;
    public $deleteId = null;

    protected $queryString = ['searchTerm'];

    public function updatedSearchTerm() { 
        $this->resetPage(); 
    }

    public function getTrashedPermissionsProperty() {
        $query = Permission::onlyTrashed()
            ->where('name', 'like', '%' . $this->searchTerm . '%')
            ->latest('deleted_at');

        // ដោះស្រាយបញ្ហា 'all' និងករណីគ្មានទិន្នន័យ (Count = 0)
        if ($this->perPage === 'all') {
            $total = $query->count();
            return $query->paginate($total > 0 ? $total : 1);
        }

        return $query->paginate((int) $this->perPage);
    }

    public function updatedSelectAll($value) {
        if ($value) {
            $this->selectedPermissions = collect($this->getTrashedPermissionsProperty()->items())
                ->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedPermissions = [];
        }
    }

    // --- Restore Actions ---
    public function restore($id) {
        Permission::onlyTrashed()->findOrFail($id)->restore();
        
        $this->dispatch('notify', type: 'success', message: __('messages.permission_restored'));
    }

    public function restoreSelected() {
        if (empty($this->selectedPermissions)) return;
        
        Permission::onlyTrashed()->whereIn('id', $this->selectedPermissions)->restore();
        $this->reset(['selectedPermissions', 'selectAll']);
        
        $this->dispatch('notify', type: 'success', message: __('messages.selected_permissions_restored'));
    }

    // --- Force Delete Actions ---
    public function confirmForceDelete($id = null) {
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function executeForceDelete() {
        if ($this->deleteId) {
            Permission::onlyTrashed()->findOrFail($this->deleteId)->forceDelete();
            $this->dispatch('notify', type: 'success', message: __('messages.permission_force_deleted'));
        } else {
            Permission::onlyTrashed()->whereIn('id', $this->selectedPermissions)->forceDelete();
            $this->reset(['selectedPermissions', 'selectAll']);
            $this->dispatch('notify', type: 'success', message: __('messages.selected_permissions_force_deleted'));
        }
        
        $this->isDeleteModalOpen = false;
        $this->deleteId = null;
    }

    public function render() {
        return view('livewire.settings.permission-trash', [
            'permissions' => $this->getTrashedPermissionsProperty()
        ]);
    }
}