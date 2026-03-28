<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class GenericTrash extends Component
{
    use WithPagination;

    public $type; 
    public $searchTerm = '';
    public $perPage = 10;
    
    public $selectedItems = [];
    public $selectAll = false;
    
    public $isDeleteModalOpen = false;
    public $deleteId = null;

    protected $queryString = ['searchTerm'];

    protected function getModelMap()
    {
        return [
            'user' => User::class,
            'role' => Role::class,
            'permission' => Permission::class,
        ];
    }

    public function mount($type)
    {
        if (!array_key_exists($type, $this->getModelMap())) abort(404);
        $this->type = $type;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $modelClass = $this->getModelMap()[$this->type];
            $this->selectedItems = $modelClass::onlyTrashed()
                ->where('name', 'like', '%' . $this->searchTerm . '%')
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function restore($id = null)
    {
        $ids = $id ? [$id] : $this->selectedItems;
        if (empty($ids)) return;

        $modelClass = $this->getModelMap()[$this->type];
        $modelClass::onlyTrashed()->whereIn('id', $ids)->restore();

        $this->reset(['selectedItems', 'selectAll']);
        $this->dispatch('notify', type: 'success', message: __('messages.restored_success') ?? 'Items restored successfully!');
    }

    public function confirmForceDelete($id = null)
    {
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function executeDelete()
    {
        $ids = $this->deleteId ? [$this->deleteId] : $this->selectedItems;
        if (empty($ids)) return;

        $modelClass = $this->getModelMap()[$this->type];
        $modelClass::onlyTrashed()->whereIn('id', $ids)->forceDelete();
        
        $this->isDeleteModalOpen = false;
        $this->reset(['selectedItems', 'selectAll', 'deleteId']);
        $this->dispatch('notify', type: 'success', message: __('messages.deleted_permanently') ?? 'Deleted permanently!');
    }

    public function render()
    {
        $modelClass = $this->getModelMap()[$this->type];
        
        $backRouteMap = [
            'user' => 'settings.users',
            'role' => 'settings.roles',
            'permission' => 'settings.permissions',
        ];

        // ជួសជុលបញ្ហា String "ALL"
        $perPageValue = (strtolower($this->perPage) === 'all') 
            ? ($modelClass::onlyTrashed()->count() > 0 ? $modelClass::onlyTrashed()->count() : 1) 
            : (int) $this->perPage;

        $items = $modelClass::onlyTrashed()
            ->where('name', 'like', '%' . $this->searchTerm . '%')
            ->latest('deleted_at')
            ->paginate($perPageValue); // ប្រើអថេរដែលបាន convert រួច

        return view('livewire.settings.generic-trash', [
            'items' => $items,
            'title' => __('messages.' . $this->type . 's_trash') ?? ucfirst($this->type) . ' Trash',
            'backRoute' => $backRouteMap[$this->type] ?? 'dashboard'
        ])->layout('layouts.app');
    }
}