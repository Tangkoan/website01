<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class PermissionActivityLog extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $perPage = 10;

    protected $queryString = ['searchTerm'];

    public function updatedSearchTerm() 
    { 
        $this->resetPage(); 
    }

    public function getActivityLogsProperty() 
    {
        $query = Activity::where('subject_type', 'App\Models\Permission')
            ->when($this->searchTerm, function($query) {
                $query->where('description', 'like', '%' . $this->searchTerm . '%')
                      ->orWhereHas('causer', function($q) {
                          $q->where('name', 'like', '%' . $this->searchTerm . '%');
                      });
            })
            ->latest();

        // ដោះស្រាយបញ្ហា 'all' (string * int)
        if ($this->perPage === 'all') {
            $totalCount = $query->count();
            // បើគ្មានទិន្នន័យទេ ដាក់លេខ 1 ដើម្បីកុំឱ្យ Error paginate(0)
            return $query->paginate($totalCount > 0 ? $totalCount : 1); 
        }

        // បំប្លែងទៅជាលេខ (int) ជានិច្ចមុនបោះចូល paginate
        return $query->paginate((int) $this->perPage); 
    }

    public function render() 
    {
        return view('livewire.settings.permission-activity-log', [
            'logs' => $this->getActivityLogsProperty()
        ]);
    }
}