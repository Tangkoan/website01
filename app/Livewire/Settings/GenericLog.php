<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

class GenericLog extends Component
{
    use WithPagination;

    public $type;
    public $searchTerm = '';
    public $perPage = 10;
    
    public $selectedLogs = [];
    public $selectAll = false;
    
    public $isModalOpen = false;
    public $selectedActivity = null;

    public $showDeleteModal = false;
    public $logToDeleteId = null;
    public $isBulkDelete = false;

    protected $queryString = ['searchTerm'];

    protected function getModelMap()
    {
        return [
            'user' => User::class,
            'role' => Role::class,
            'permission' => Permission::class,
            'category' => \App\Models\Category::class,
            'brand' => \App\Models\Brand::class,
            'sidebar' => \App\Models\Sidebar::class,

        ];
    }

    public function mount($type)
    {
        if (!array_key_exists($type, $this->getModelMap())) abort(404);
        
        $this->type = $type;

        // បន្ថែមជញ្ជាំងការពារការចូលមើល Route (Route Protection)
        // ទាមទារសិទ្ធិ 'view-user-logs', 'view-role-logs' ឬ 'view-permission-logs'
        abort_if(Gate::denies("view-{$this->type}-logs"), 403);
    }

    // ==========================================
    // Core Logic: Query សម្រាប់ត្រួតពិនិត្យ Level
    // ==========================================
    private function getBaseQuery()
    {
        $modelClass = $this->getModelMap()[$this->type];
        
        // យក Level ខ្ពស់បំផុតរបស់ User បច្ចុប្បន្ន (បើគ្មានកំណត់ជា 0)
        $myLevel = auth()->user()->roles->max('level') ?? 0;

        $query = Activity::where('subject_type', $modelClass)
            ->when($this->searchTerm, function ($q) {
                $q->where('description', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHasMorph('causer', [User::class], function ($q2) {
                      $q2->withTrashed()->where('name', 'like', '%' . $this->searchTerm . '%');
                  })
                  // បន្ថែមការស្វែងរកតាម Properties ព្រោះពេល User ត្រូវ Force Delete យើងនៅសល់តែ Properties ទេ
                  ->orWhere('properties->attributes->name', 'like', '%' . $this->searchTerm . '%');
            })
            // 1. ត្រួតពិនិត្យលើអ្នកធ្វើសកម្មភាព (Causer)
            ->where(function ($q) use ($myLevel) {
                $q->whereNull('causer_id') // អនុញ្ញាតឲ្យឃើញ Log របស់ System
                  ->orWhereHasMorph('causer', [User::class], function ($userQuery) use ($myLevel) {
                      // ទាញយកតែ Causer ណាដែល "គ្មាន Role Level ណាដែលធំជាង Level របស់ខ្ញុំ"
                      $userQuery->withTrashed()->whereDoesntHave('roles', function ($roleQuery) use ($myLevel) {
                          $roleQuery->where('level', '>', $myLevel);
                      });
                  })
                  // ✅ ចំណុចទី១៖ អនុញ្ញាតឱ្យបង្ហាញ Log របស់ Causer ដែលត្រូវ Force Delete បាត់ពី DB 
                  ->orWhereDoesntHaveMorph('causer', [User::class]); 
            });

        // 2. ត្រួតពិនិត្យលើប្រធានបទដែលរងអំពើ (Subject: User ឬ Role)
        if ($this->type === 'user') {
            $query->where(function($q) use ($myLevel) {
                 $q->whereNull('subject_id')
                   ->orWhereHasMorph('subject', [User::class], function ($userQuery) use ($myLevel) {
                       $userQuery->withTrashed()->whereDoesntHave('roles', function ($roleQuery) use ($myLevel) {
                           $roleQuery->where('level', '>', $myLevel);
                       });
                 })
                 // ✅ ចំណុចទី២៖ អនុញ្ញាតឱ្យបង្ហាញ Log របស់ User ដែលត្រូវ Force Delete បាត់ពី DB 
                 ->orWhereDoesntHaveMorph('subject', [User::class]);
            });
        } elseif ($this->type === 'role') {
            $query->where(function($q) use ($myLevel) {
                 $q->whereNull('subject_id')
                   ->orWhereHasMorph('subject', [Role::class], function ($roleQuery) use ($myLevel) {
                       $roleQuery->withTrashed()->where('level', '<=', $myLevel);
                 })
                 // ✅ ចំណុចទី៣៖ អនុញ្ញាតឱ្យបង្ហាញ Log របស់ Role ដែលត្រូវ Force Delete 
                 ->orWhereDoesntHaveMorph('subject', [Role::class]);
            });
        }

        return $query;
    }

    public function confirmDelete($id)
    {
        abort_if(Gate::denies("delete-{$this->type}-logs"), 403);

        $this->logToDeleteId = $id;
        $this->isBulkDelete = false;
        $this->showDeleteModal = true;
    }

    public function confirmBulkDelete()
    {
        abort_if(Gate::denies("delete-{$this->type}-logs"), 403);

        $this->isBulkDelete = true;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->logToDeleteId = null;
        $this->isBulkDelete = false;
    }

    public function executeDelete()
    {
        abort_if(Gate::denies("delete-{$this->type}-logs"), 403);

        if ($this->isBulkDelete) {
            $this->bulkDelete();
            $message = __('messages.deleted_successfully') ?? 'Logs deleted successfully!';
        } else {
            $this->deleteLog($this->logToDeleteId);
            $message = __('messages.deleted_successfully') ?? 'Log deleted successfully!';
        }
        
        $this->closeDeleteModal();
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function deleteLog($id)
    {
        Activity::find($id)?->delete();
        $this->selectedLogs = array_diff($this->selectedLogs, [$id]);
    }

    public function bulkDelete()
    {
        if (count($this->selectedLogs) > 0) {
            Activity::whereIn('id', $this->selectedLogs)->delete();
            $this->selectedLogs = [];
            $this->selectAll = false;
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // ហៅកូដ Query ដែលមានត្រួតពិនិត្យ Level រួចជាស្រេច
            $query = $this->getBaseQuery()->latest();

            $perPageValue = (strtolower($this->perPage) === 'all') 
                ? ($query->count() > 0 ? $query->count() : 1) 
                : (int) $this->perPage;

            $this->selectedLogs = $query->paginate($perPageValue)
                                        ->pluck('id')
                                        ->map(fn($id) => (string) $id)
                                        ->toArray();
        } else {
            $this->selectedLogs = [];
        }
    }

    public function updatingPage()
    {
        $this->selectAll = false;
        $this->selectedLogs = [];
    }

    public function viewDetails($id)
    {
        $this->selectedActivity = Activity::find($id);
        $this->isModalOpen = true;
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
        // ហៅកូដ Query ដែលមានត្រួតពិនិត្យ Level រួចជាស្រេច ហើយភ្ជាប់ Relationship
        $query = $this->getBaseQuery()->with(['causer', 'subject'])->latest();

        $perPageValue = (strtolower($this->perPage) === 'all') 
            ? ($query->count() > 0 ? $query->count() : 1) 
            : (int) $this->perPage;

        $activities = $query->paginate($perPageValue);

        $titleKey = 'messages.' . strtolower($this->type) . '_activity_logs';

       return view('livewire.settings.generic-log', [
            'activities' => $activities,
            'title' => __($titleKey), 
            'backRoute' => $this->getBackRoute(),
        ])->layout('layouts.app')->title(__('messages.logs'));
    }
}