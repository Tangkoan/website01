<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Services\UserService;
use Livewire\WithPagination;

class UserTrash extends Component
{
    use WithPagination;

    public $searchTerm = '', $perPage = 10, $selectedUsers = [], $selectAll = false;
    public $isDeleteModalOpen = false, $deleteId = null;

    protected function service()
    {
        return app(UserService::class);
    }

    public function restore($id = null)
    {
        $ids = $id ?: $this->selectedUsers;
        $this->service()->restoreUsers($ids);
        $this->reset(['selectedUsers', 'selectAll']);
        $this->dispatch('notify', type: 'success', message: __('messages.restored') ?? 'Users Restored!');
    }

    public function confirmForceDelete($id = null)
    {
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function executeDelete()
    {
        $ids = $this->deleteId ?: $this->selectedUsers;
        $this->service()->forceDeleteUsers($ids);
        $this->reset(['selectedUsers', 'selectAll', 'deleteId', 'isDeleteModalOpen']);
        $this->dispatch('notify', type: 'success', message: __('messages.permanent_deleted') ?? 'Users permanently deleted!');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedUsers = collect($this->service()->getTrashedUsers($this->searchTerm, $this->perPage)->items())
                ->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        $myMaxLevel = auth()->user()->roles->max('level') ?? 0;
        return view('livewire.settings.user.user-trash', [
            'users' => $this->service()->getTrashedUsers($this->searchTerm, $this->perPage, $myMaxLevel)
        ])->layout('layouts.app');
    }
}