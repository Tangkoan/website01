<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Permission; // ឬ Spatie\Permission\Models\Permission

class PermissionTrash extends Component
{
    use WithPagination;

    // ទាញយកទិន្នន័យដែលបានលុបមកបង្ហាញ
    public function getTrashedPermissionsProperty()
    {
        return Permission::onlyTrashed()->latest('deleted_at')->paginate(10);
    }

    // ទាញយកទិន្នន័យត្រឡប់មកវិញ
    public function restore($id)
    {
        $permission = Permission::onlyTrashed()->findOrFail($id);
        $permission->restore();
        
        session()->flash('message', 'ទិន្នន័យត្រូវបាន Restore ជោគជ័យ!');
    }

    // លុបចោលជាអចិន្ត្រៃយ៍ (លុបចេញពី Database ពិតប្រាកដ)
    public function forceDelete($id)
    {
        $permission = Permission::onlyTrashed()->findOrFail($id);
        $permission->forceDelete();
        
        session()->flash('message', 'ទិន្នន័យត្រូវបានលុបជាអចិន្ត្រៃយ៍!');
    }

    public function render()
    {
        return view('livewire.permission-trash', [
            'permissions' => $this->trashedPermissions
        ]);
    }
}