<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class GenericLog extends Component
{
    use WithPagination;

    public $type;
    public $searchTerm = '';
    public $perPage = 10;
    
    // Properties សម្រាប់ Bulk Actions
    public $selectedLogs = [];
    public $selectAll = false;
    
    // Properties សម្រាប់ View Details Modal
    public $isModalOpen = false;
    public $selectedActivity = null;

    // Properties សម្រាប់ Custom Delete Modal
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
        ];
    }

    public function mount($type)
    {
        if (!array_key_exists($type, $this->getModelMap())) abort(404);
        $this->type = $type;
    }

    // --- ចាប់ផ្តើមមុខងារគ្រប់គ្រង Delete Modal ---
    
    // បើក Modal ពេលចុចលុបមួយៗ
    public function confirmDelete($id)
    {
        $this->logToDeleteId = $id;
        $this->isBulkDelete = false;
        $this->showDeleteModal = true;
    }

    // បើក Modal ពេលចុច Bulk Delete
    public function confirmBulkDelete()
    {
        $this->isBulkDelete = true;
        $this->showDeleteModal = true;
    }

    // បិទ Delete Modal
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->logToDeleteId = null;
        $this->isBulkDelete = false;
    }

    // ដំណើរការលុបពិតប្រាកដពេលចុច Confirm ក្នុង Modal
    public function executeDelete()
    {
        if ($this->isBulkDelete) {
            $this->bulkDelete();
        } else {
            $this->deleteLog($this->logToDeleteId);
        }
        
        $this->closeDeleteModal();
    }

    // --- ចាប់ផ្តើមមុខងារ Delete & Bulk Delete (ដំណើរការទិន្នន័យ) ---
    
    public function deleteLog($id)
    {
        Activity::find($id)?->delete();
        $this->selectedLogs = array_diff($this->selectedLogs, [$id]); // ដកចេញពី array បើវាត្រូវបាន select
    }

    public function bulkDelete()
    {
        if (count($this->selectedLogs) > 0) {
            Activity::whereIn('id', $this->selectedLogs)->delete();
            $this->selectedLogs = []; // Reset វិញ
            $this->selectAll = false;
        }
    }

    // មុខងារ Select All ក្នុង Page នីមួយៗ
    public function updatedSelectAll($value)
    {
        if ($value) {
            $modelClass = $this->getModelMap()[$this->type];
            
            // កសាង Query ដូចគ្នានឹងក្នុង render()
            $query = Activity::where('subject_type', $modelClass)
                ->when($this->searchTerm, function ($q) {
                    $q->where('description', 'like', '%' . $this->searchTerm . '%')
                      ->orWhereHasMorph('causer', [User::class], function ($q2) {
                          $q2->where('name', 'like', '%' . $this->searchTerm . '%');
                      });
                })
                ->latest();

            // កំណត់ចំនួន Per Page
            $perPageValue = (strtolower($this->perPage) === 'all') 
                ? ($query->count() > 0 ? $query->count() : 1) 
                : (int) $this->perPage;

            // ប្រើ paginate() ជំនួសការទាញយកទាំងអស់ ដើម្បីយកតែ ID លើ Page បច្ចុប្បន្ន
            $this->selectedLogs = $query->paginate($perPageValue)
                                        ->pluck('id')
                                        ->map(fn($id) => (string) $id) // បំប្លែងទៅជា String ដើម្បីឲ្យត្រូវជាមួយ Checkbox value
                                        ->toArray();
        } else {
            $this->selectedLogs = [];
        }
    }

    // បន្ថែមសម្រាប់ UX
    public function updatingPage()
    {
        $this->selectAll = false;
        $this->selectedLogs = [];
    }

    // --- មុខងារ View Details ---

    public function viewDetails($id)
    {
        $this->selectedActivity = Activity::find($id);
        $this->isModalOpen = true;
    }

    // --- កំណត់ Route សម្រាប់ប៊ូតុង Back ទៅតាមប្រភេទ (Type) ---
    public function getBackRoute()
    {
        // ទីនេះអ្នកត្រូវប្រាកដថាឈ្មោះ Route ត្រូវនឹងឈ្មោះក្នុង web.php របស់អ្នក
        $routes = [
            'user' => 'settings.users', // ឧទាហរណ៍៖ បើ route ឈ្មោះផ្សេង សូមដូរទីនេះ
            'role' => 'settings.roles',
            'permission' => 'settings.permissions',
        ];

        return $routes[$this->type] ?? 'dashboard';
    }

    public function render()
    {
        $modelClass = $this->getModelMap()[$this->type];

        $query = Activity::with(['causer', 'subject'])
            ->where('subject_type', $modelClass)
            ->when($this->searchTerm, function ($q) {
                $q->where('description', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHasMorph('causer', [User::class], function ($q2) {
                      $q2->where('name', 'like', '%' . $this->searchTerm . '%');
                  });
            })
            ->latest();

        $perPageValue = (strtolower($this->perPage) === 'all') 
            ? ($query->count() > 0 ? $query->count() : 1) 
            : (int) $this->perPage;

        $activities = $query->paginate($perPageValue);

        $titleKey = 'messages.' . strtolower($this->type) . '_activity_logs';

       return view('livewire.settings.generic-log', [
            'activities' => $activities,
            // ✅ ប្រើមុខងារ __() ដើម្បីទាញយកភាសា
            'title' => __($titleKey), 
            'backRoute' => $this->getBackRoute(),
        ])->layout('layouts.app');
    }
}