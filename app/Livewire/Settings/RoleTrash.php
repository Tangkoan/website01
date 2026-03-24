<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Services\RoleService;
use Livewire\WithPagination;

class RoleTrash extends Component
{
    use WithPagination;

    // States
    public $searchTerm = '', $perPage = 10, $selectedRoles = [], $selectAll = false;
    public $isDeleteModalOpen = false, $deleteId = null;

    protected function service()
    {
        return app(RoleService::class);
    }

    // ហៅបោះបង់ (Restore)
    public function restore($id = null)
    {
        $ids = $id ?: $this->selectedRoles;
        $this->service()->restoreRoles($ids);
        $this->reset(['selectedRoles', 'selectAll']);
        $this->dispatch('notify', type: 'success', message: __('messages.restored'));
    }

    // បញ្ជាក់លុបចោលទាំងស្រុង (Force Delete Confirmation)
    public function confirmForceDelete($id = null)
    {
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    // អនុវត្តលុបចោលទាំងស្រុង (Force Delete)
    public function executeDelete()
    {
        $ids = $this->deleteId ?: $this->selectedRoles;
        $this->service()->forceDeleteRoles($ids);
        $this->reset(['selectedRoles', 'selectAll', 'deleteId', 'isDeleteModalOpen']);
        $this->dispatch('notify', type: 'success', message: __('messages.permanent_deleted'));
    }

    // Logic សម្រាប់ Select All
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedRoles = collect($this->service()->getTrashedRoles($this->searchTerm, $this->perPage)->items())
                ->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedRoles = [];
        }
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.settings.role.role-trash', [
            'roles' => $this->service()->getTrashedRoles($this->searchTerm, $this->perPage)
        ])->layout('layouts.app'); // ឬ layout របស់បង
    }
}