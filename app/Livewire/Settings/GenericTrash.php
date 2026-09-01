<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

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
            'brand' => \App\Models\Brand::class,
            'sidebar' => \App\Models\Sidebar::class,
            'category' => \App\Models\Product\Category::class,
            'brand' => \App\Models\Product\Brand::class,
            'categories' => \App\Models\Story\Categories::class,
            'category' => \App\Models\Story\Category::class,
            'category' => \App\Models\Category::class,
            'tag' => \App\Models\Tag::class,



        ];
    }

    public function mount($type)
    {
        if (!array_key_exists($type, $this->getModelMap())) abort(404);
        
        $this->type = $type;

        // ការពារការចូលមើលទំព័រ (Route Protection)
        // ឧទាហរណ៍: ទាមទារសិទ្ធិ 'view-user-trash', 'view-role-trash' ឬ 'view-permission-trash'
        abort_if(Gate::denies("view-{$this->type}-trash"), 403);
    }

    // ==========================================
    // Core Logic: Query សម្រាប់ត្រួតពិនិត្យ Level
    // ==========================================
    private function getBaseQuery()
    {
        $modelClass = $this->getModelMap()[$this->type];
        
        // យក Level ខ្ពស់បំផុតរបស់ User បច្ចុប្បន្ន (បើគ្មានកំណត់ជា 0)
        $myLevel = auth()->user()->roles->max('level') ?? 0;

        $query = $modelClass::onlyTrashed()
            ->when($this->searchTerm, function ($q) {
                // ស្វែងរកតាមឈ្មោះ ឬអុីមែល (បើជា User)
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->when($this->type === 'user', function($q2) {
                      $q2->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                  });
            });

        // ត្រួតពិនិត្យ Level សម្រាប់ User និង Role
        if ($this->type === 'user') {
            $query->whereDoesntHave('roles', function ($roleQuery) use ($myLevel) {
                // មិនឱ្យឃើញ User ដែលមាន Role Level ខ្ពស់ជាងខ្លួន
                $roleQuery->where('level', '>', $myLevel);
            });
        } elseif ($this->type === 'role') {
            // អនុញ្ញាតឱ្យឃើញតែ Role ដែលមាន Level តូចជាង ឬស្មើខ្លួន
            $query->where('level', '<=', $myLevel);
        }
        // បើជា Permission គឺមិនបាច់ឆែក Level ទេ ព្រោះ Permission អត់មាន Level

        return $query;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // ប្រើ getBaseQuery ដើម្បីកុំឱ្យ Select ចំ Item ដែលគ្មានសិទ្ធិមើល
            $query = $this->getBaseQuery();
            
            $perPageValue = (strtolower($this->perPage) === 'all') 
                ? ($query->count() > 0 ? $query->count() : 1) 
                : (int) $this->perPage;

            $this->selectedItems = $query->paginate($perPageValue)
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatingPage()
    {
        $this->selectAll = false;
        $this->selectedItems = [];
    }

    public function restore($id = null)
    {
        abort_if(Gate::denies("restore-{$this->type}"), 403);

        $ids = $id ? [$id] : $this->selectedItems;
        if (empty($ids)) return;

        // កែមកប្រើទម្រង់នេះ ដើម្បីឲ្យ Model Event ដើរ និងចាប់ Log បាន
        $items = $this->getBaseQuery()->whereIn('id', $ids)->get();
        foreach ($items as $item) {
            $item->restore();
        }

        $this->reset(['selectedItems', 'selectAll']);
        $this->dispatch('notify', type: 'success', message: __('messages.restored_success') ?? 'Items restored successfully!');
    }

    public function executeDelete()
    {
        abort_if(Gate::denies("force-delete-{$this->type}"), 403);

        $ids = $this->deleteId ? [$this->deleteId] : $this->selectedItems;
        if (empty($ids)) return;

        // កែមកប្រើទម្រង់នេះ ដើម្បីឲ្យ Model Event ដើរ និងចាប់ Log បាន
        $items = $this->getBaseQuery()->whereIn('id', $ids)->get();
        foreach ($items as $item) {
            $item->forceDelete();
        }
        
        $this->isDeleteModalOpen = false;
        $this->reset(['selectedItems', 'selectAll', 'deleteId']);
        $this->dispatch('notify', type: 'success', message: __('messages.deleted_permanently') ?? 'Deleted permanently!');
    }

    public function confirmForceDelete($id = null)
    {
        abort_if(Gate::denies("force-delete-{$this->type}"), 403);

        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function getBackRoute()
    {
        $routes = [
            'user' => 'settings.users',
            'role' => 'settings.roles',
            'permission' => 'settings.permissions',



        ];

        return $routes[$this->type] ?? 'dashboard';
    }

    

    public function render()
    {
        $backRouteMap = [
            'user' => 'settings.users',
            'role' => 'settings.roles',
            'permission' => 'settings.permissions',
            'category' => 'product.categories.index',
            'brand' => 'product.brands.index',
            'sidebar' => 'settings.sidebars.index',
            'category' => 'categories.index',
            'tag' => 'tags.index',
        ];

        // ហៅ BaseQuery មកប្រើ
        $query = $this->getBaseQuery()->latest('deleted_at');

        $perPageValue = (strtolower($this->perPage) === 'all') 
            ? ($query->count() > 0 ? $query->count() : 1) 
            : (int) $this->perPage;

        $items = $query->paginate($perPageValue); 

        return view('livewire.settings.generic-trash', [
            'items' => $items,
            'title' => __('messages.' . $this->type . 's_trash') ?? ucfirst($this->type) . ' Trash',
            'backRoute' => $backRouteMap[$this->type] ?? 'dashboard'
        ])->layout('layouts.app')->title(__('messages.trash'));
    }
}