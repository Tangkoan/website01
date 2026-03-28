<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Services\UserService;
use Livewire\WithPagination;

class UserLogs extends Component
{
    use WithPagination;

    public $perPage = 10, $searchTerm = '';
    protected $paginationTheme = 'tailwind';

    protected function service()
    {
        return app(UserService::class);
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    

    public function render()
    {
        // ១. ចាប់យក Level ខ្ពស់បំផុតរបស់ខ្លួនឯង
        $myMaxLevel = auth()->user()->roles->max('level') ?? 0;

        return view('livewire.settings.user.user-logs', [
            // ២. បញ្ជូន $myMaxLevel ទៅក្នុង Method getUserLogs
            'logs' => $this->service()->getUserLogs($this->searchTerm, $this->perPage, $myMaxLevel)
        ])->layout('layouts.app');
    }
}