<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class ActivityLogManager extends Component
{
    use WithPagination;

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

    public function mount()
    {
        // ទាមទារសិទ្ធិ 'view-activity-logs' សម្រាប់មើលទំព័ររួមនេះ
        abort_if(Gate::denies("view-activity-logs"), 403);
    }

    // ==========================================
    // Core Logic: Query ទាញយក Log ទាំងអស់
    // ==========================================
    private function getBaseQuery()
    {
        $myLevel = auth()->user()->roles->max('level') ?? 0;

        return Activity::when($this->searchTerm, function ($q) {
                $q->where('description', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('log_name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHasMorph('causer', [User::class], function ($q2) {
                      $q2->withTrashed()->where('name', 'like', '%' . $this->searchTerm . '%');
                  })
                  ->orWhere('properties->attributes->name', 'like', '%' . $this->searchTerm . '%');
            })
            // ត្រួតពិនិត្យលើអ្នកធ្វើសកម្មភាព (Causer) កុំឱ្យឃើញ Log អ្នកធំជាងខ្លួន
            ->where(function ($q) use ($myLevel) {
                $q->whereNull('causer_id') 
                  ->orWhereHasMorph('causer', [User::class], function ($userQuery) use ($myLevel) {
                      $userQuery->withTrashed()->whereDoesntHave('roles', function ($roleQuery) use ($myLevel) {
                          $roleQuery->where('level', '>', $myLevel);
                      });
                  })
                  ->orWhereDoesntHaveMorph('causer', [User::class]); 
            });
    }

    public function confirmDelete($id)
    {
        abort_if(Gate::denies("delete-activity-logs"), 403);

        $this->logToDeleteId = $id;
        $this->isBulkDelete = false;
        $this->showDeleteModal = true;
    }

    public function confirmBulkDelete()
    {
        abort_if(Gate::denies("delete-activity-logs"), 403);

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
        abort_if(Gate::denies("delete-activity-logs"), 403);

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

    public function render()
    {
        $query = $this->getBaseQuery()->with(['causer', 'subject'])->latest();

        // 💡 កែប្រែនៅត្រង់នេះ
        // បើអ្នករើស 'all' យើងយកចំនួនទិន្នន័យសរុបធ្វើជា PerPage
        // បើទិន្នន័យសរុបស្មើ 0 យើងដាក់ 1 ដើម្បីកុំឱ្យ Error ចែកនឹងសូន្យ
        $totalCount = $query->count();
        $perPageValue = (strtolower($this->perPage) === 'all') 
            ? ($totalCount > 0 ? $totalCount : 1) 
            : (int) $this->perPage;

        $activities = $query->paginate($perPageValue);

        return view('livewire.settings.activity-log-manager', [
            'activities' => $activities,
            'title' => __('messages.all_activity_logs') ?? 'All Activity Logs', 
        ])->layout('layouts.app')->title(__('messages.logs'));
    }
}