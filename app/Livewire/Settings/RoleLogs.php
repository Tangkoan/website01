<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Services\RoleService;
use Livewire\WithPagination;



class RoleLogs extends Component
{
    use WithPagination;

    public $perPage = 10, $searchTerm = '';

    // 3. ប្រសិនបើបងប្រើ Tailwind (TALL Stack) គួរកំណត់បែបនេះ
    protected $paginationTheme = 'tailwind';

    

    protected function service()
    {
        return app(RoleService::class);
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }
    

    public function render()
    {
        return view('livewire.settings.role.role-logs', [
            // ត្រូវប្រាកដថានៅក្នុង RoleService មាន method getRoleLogs($searchTerm, $perPage)
            'logs' => app(RoleService::class)->getRoleLogs($this->searchTerm, $this->perPage)
        ])->layout('layouts.app');
    }
}