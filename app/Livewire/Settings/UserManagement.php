<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserManagement extends Component
{
    use WithPagination;

    // Form Properties
    public $userId, $name, $email, $password;
    public $selectedRoles = [];
    
    // UI States
    public $isModalOpen = false;
    public $searchTerm = '';
    
    // Bulk Action Properties
    public $selectedUsers = [];
    public $selectAll = false;

    protected $updatesQueryString = ['searchTerm'];

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $myMaxLevel = auth()->user()->roles->max('level') ?? 0;
            
            // រើសយកតែ User ដែលយើងមានសិទ្ធិចាត់ចែង (Level ទាបជាងយើង)
            $this->selectedUsers = User::whereHas('roles', function($q) use ($myMaxLevel) {
                $q->where('level', '<', $myMaxLevel);
            })
            ->where('id', '!=', auth()->id())
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function render()
    {
        $myMaxLevel = auth()->user()->roles->max('level') ?? 0;

        // ទាញយក Role ដែលយើងមានសិទ្ធិផ្ដល់ឱ្យគេ (Level <= ខ្លួនឯង)
        $availableRoles = Role::where('level', '<=', $myMaxLevel)->orderBy('level', 'desc')->get();

        // Query ស្វែងរក User
        $users = User::with('roles')
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.settings.user-management', [
            'users' => $users,
            'availableRoles' => $availableRoles,
            'myMaxLevel' => $myMaxLevel
        ]);
    }

    public function openModal()
    {
        $this->resetErrorBag();
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function resetFields()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->selectedRoles = [];
    }

    public function editUser($id)
    {
        $this->resetErrorBag();
        $user = User::findOrFail($id);
        
        $myMaxLevel = auth()->user()->roles->max('level') ?? 0;
        $targetMaxLevel = $user->roles->max('level') ?? 0;

        if ($targetMaxLevel >= $myMaxLevel && !auth()->user()->hasRole('Super Admin')) {
            $this->dispatch('notify', type: 'error', message: 'អ្នកមិនមានសិទ្ធិកែប្រែ User នេះទេ!');
            return;
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->roles->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $this->isModalOpen = true;
    }

    public function saveUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => $this->userId ? 'nullable|min:8' : 'required|min:8',
            'selectedRoles' => 'required|array|min:1'
        ]);

        $myMaxLevel = auth()->user()->roles->max('level') ?? 0;

        // ការពារកុំឱ្យផ្ដល់ Role ខ្ពស់ជាងខ្លួនឯង
        foreach ($this->selectedRoles as $roleId) {
            $role = Role::findById($roleId);
            if ($role->level > $myMaxLevel && !auth()->user()->hasRole('Super Admin')) {
                $this->addError('selectedRoles', 'អ្នកមិនអាចផ្ដល់ Role ដែលមានកម្រិតខ្ពស់ជាងអ្នកបានទេ។');
                return;
            }
        }

        DB::transaction(function () {
            $user = User::updateOrCreate(['id' => $this->userId], [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password ? Hash::make($this->password) : User::find($this->userId)->password,
            ]);

            $user->syncRoles($this->selectedRoles);
        });

        $this->isModalOpen = false;
        $this->dispatch('notify', type: 'success', message: 'ទិន្នន័យត្រូវបានរក្សាទុក!');
        $this->resetFields();
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $myMaxLevel = auth()->user()->roles->max('level') ?? 0;
        $targetMaxLevel = $user->roles->max('level') ?? 0;

        if ($targetMaxLevel >= $myMaxLevel && !auth()->user()->hasRole('Super Admin')) {
            $this->dispatch('notify', type: 'error', message: 'អ្នកមិនមានសិទ្ធិលុប User នេះទេ!');
            return;
        }

        $user->delete();
        $this->dispatch('notify', type: 'success', message: 'លុបបានជោគជ័យ!');
    }

    public function deleteSelected()
    {
        $myMaxLevel = auth()->user()->roles->max('level') ?? 0;

        User::whereIn('id', $this->selectedUsers)->get()->each(function($user) use ($myMaxLevel) {
            if ($user->roles->max('level') < $myMaxLevel || auth()->user()->hasRole('Super Admin')) {
                $user->delete();
            }
        });

        $this->selectedUsers = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'success', message: 'លុប User ដែលបានរើសរួចរាល់!');
    }
}