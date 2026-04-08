<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

class GlobalTrashManager extends Component
{
    use WithPagination;

    public $activeTab = 'user';
    public $searchTerm = '';
    public $perPage = 10;
    
    public $selectedItems = [];
    public $selectAll = false;
    
    public $isDeleteModalOpen = false;
    public $deleteId = null;

    protected $queryString = ['searchTerm', 'activeTab'];

    // ==========================================
    // 💡 THE MASTER CONFIG (រៀបចំសិទ្ធិទាំងអស់នៅទីនេះ)
    // ==========================================
    public function getTrashModulesProperty()
    {
        return [
            'user' => [
                'model' => User::class,
                'icon'  => '👤',
                'label' => __('messages.users') ?? 'Users',
                'permissions' => [
                    'view'    => 'view-user-trash',
                    'restore' => 'restore-user',
                    'delete'  => 'force-delete-user',
                ]
            ],
            'role' => [
                'model' => Role::class,
                'icon'  => '🛡️',
                'label' => __('messages.roles') ?? 'Roles',
                'permissions' => [
                    'view'    => 'view-role-trash',
                    'restore' => 'restore-role',
                    'delete'  => 'force-delete-role',
                ]
            ],
            'permission' => [
                'model' => Permission::class,
                'icon'  => '🔑',
                'label' => __('messages.permissions') ?? 'Permissions',
                'permissions' => [
                    'view'    => 'view-permission-trash',
                    'restore' => 'restore-permission',
                    'delete'  => 'force-delete-permission',
                ]
            ],
            'category' => [
                'model' => \App\Models\Category::class,
                'icon'  => '📦',
                'label' => __('messages.category_management') ?? 'Categories',
                'permissions' => [
                    'view'    => 'view-category-trash',
                    'restore' => 'restore-category',
                    'delete'  => 'force-delete-category',
                ]
            ],
            'brand' => [
                'model' => \App\Models\Brand::class,
                'icon'  => '📦',
                'label' => __('messages.brand_management') ?? 'Brands',
                'permissions' => [
                    'view'    => 'view-brand-trash',
                    'restore' => 'restore-brand',
                    'delete'  => 'force-delete-brand',
                ]
            ],
            
        ];
    }

    public function mount()
    {
        $modules = $this->trashModules;
        
        // ឆែកមើលថាតើ User មានសិទ្ធិមើល Tab បច្ចុប្បន្នឬអត់
        if (!array_key_exists($this->activeTab, $modules) || Gate::denies($modules[$this->activeTab]['permissions']['view'])) {
            $hasAccess = false;
            // រុញទៅ Tab ផ្សេងដែលមានសិទ្ធិ
            foreach ($modules as $key => $module) {
                if (Gate::allows($module['permissions']['view'])) {
                    $this->activeTab = $key;
                    $hasAccess = true;
                    break;
                }
            }
            abort_if(!$hasAccess, 403); 
        }
    }

    public function updatedActiveTab()
    {
        abort_if(Gate::denies($this->trashModules[$this->activeTab]['permissions']['view']), 403);
        $this->reset(['searchTerm', 'selectedItems', 'selectAll']);
        $this->resetPage();
    }

    private function getBaseQuery()
    {
        $modelClass = $this->trashModules[$this->activeTab]['model'];
        $myLevel = auth()->user()->roles->max('level') ?? 0;

        $query = $modelClass::onlyTrashed()
            ->when($this->searchTerm, function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->when($this->activeTab === 'user', function($q2) {
                      $q2->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                  });
            });

        // ត្រួតពិនិត្យ Level
        if ($this->activeTab === 'user') {
            $query->whereDoesntHave('roles', function ($roleQuery) use ($myLevel) {
                $roleQuery->where('level', '>', $myLevel);
            });
        } elseif ($this->activeTab === 'role') {
            $query->where('level', '<=', $myLevel);
        }

        return $query;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $query = $this->getBaseQuery();
            $perPageValue = (strtolower($this->perPage) === 'all') ? ($query->count() > 0 ? $query->count() : 1) : (int) $this->perPage;
            $this->selectedItems = $query->paginate($perPageValue)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatingPage()
    {
        $this->selectAll = false;
        $this->selectedItems = [];
    }

    // ==========================================
    // Core Actions (ប្រើសិទ្ធិតាម Master Config)
    // ==========================================
    public function restore($id = null)
    {
        abort_if(Gate::denies($this->trashModules[$this->activeTab]['permissions']['restore']), 403);

        $ids = $id ? [$id] : $this->selectedItems;
        if (empty($ids)) return;

        $items = $this->getBaseQuery()->whereIn('id', $ids)->get();
        foreach ($items as $item) {
            $item->restore();
        }

        $this->reset(['selectedItems', 'selectAll']);
        $this->dispatch('notify', type: 'success', message: __('messages.restored_success') ?? 'Items restored successfully!');
    }

    public function confirmForceDelete($id = null)
    {
        abort_if(Gate::denies($this->trashModules[$this->activeTab]['permissions']['delete']), 403);
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function executeDelete()
    {
        abort_if(Gate::denies($this->trashModules[$this->activeTab]['permissions']['delete']), 403);

        $ids = $this->deleteId ? [$this->deleteId] : $this->selectedItems;
        if (empty($ids)) return;

        $items = $this->getBaseQuery()->whereIn('id', $ids)->get();
        foreach ($items as $item) {
            $item->forceDelete();
        }
        
        $this->isDeleteModalOpen = false;
        $this->reset(['selectedItems', 'selectAll', 'deleteId']);
        $this->dispatch('notify', type: 'success', message: __('messages.deleted_permanently') ?? 'Deleted permanently!');
    }

    public function render()
    {
        $query = $this->getBaseQuery()->latest('deleted_at');

        $perPageValue = (strtolower($this->perPage) === 'all') 
            ? ($query->count() > 0 ? $query->count() : 1) 
            : (int) $this->perPage;

        $items = $query->paginate($perPageValue); 

        return view('livewire.settings.global-trash-manager', [
            'items' => $items,
            'title' => __('messages.recycle_bin') ?? 'Recycle Bin',
        ])->layout('layouts.app')->title(__('messages.trash'));
    }
}