<?php

namespace App\Services;

// use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Spatie\Activitylog\Models\Activity;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class RoleService
{

    /**
     * ទាញយក Level ខ្ពស់បំផុតរបស់អ្នកប្រើប្រាស់ដែលកំពុង Login
     * បើគ្មាន Role (ករណីមិនទាន់មានសិទ្ធិសោះ) កំណត់ត្រឹម 1 ជាលំនាំដើម
     */
    public function getMaxAllowedLevelForCurrentUser()
    {
        $user = Auth::user();
        
        if (!$user) {
            return 1; 
        }

        // សន្មតថាអ្នកកំពុងប្រើ Spatie Permission ហើយ User មាន relationship ជាមួយ roles()
        // បើលេខ Level កាន់តែធំមានន័យថាសិទ្ធិកាន់តែធំ យក max()
        $maxLevel = $user->roles()->max('level');
        
        // ប្រសិនបើ User ជា Super Admin (អ្នកអាចមាន Logic ឆែក Super Admin ផ្សេង) 
        // ឧទាហរណ៍បើ Super Admin អាចដាក់ Level ប៉ុន្មានក៏បាន (ឧ. ដល់ 100) អ្នកអាចបន្ថែមលក្ខខណ្ឌនៅទីនេះ
        // if ($user->hasRole('Super Admin')) { return 100; }

        return $maxLevel ?? 1;
    }

    /**
     * ទាញយក Role ដែលបានលុប (Soft Deleted)
     */
    public function getTrashedRoles($searchTerm, $perPage)
    {
        $maxLevel = $this->getMaxAllowedLevelForCurrentUser();

        return Role::onlyTrashed()
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->where('level', '<=', $maxLevel) // បិទមិនឲ្យឃើញ Role ធំៗនៅក្នុង Trash ដូចគ្នា
            ->paginate((int)$perPage);
    }
    
    /**
     * ស្តារទិន្នន័យឡើងវិញ (Restore)
     */
    public function restoreRoles($ids) {
        $ids = is_array($ids) ? $ids : [$ids];
        return Role::onlyTrashed()->whereIn('id', $ids)->get()->each(function($role) {
            $role->restore(); 
        });
    }

    /**
     * លុបចោលទាំងស្រុង (Force Delete)
     */
    public function forceDeleteRoles($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return Role::onlyTrashed()->whereIn('id', $ids)->forceDelete();
    }

    /**
     * ទាញយក Activity Logs របស់ Role
     */
    public function getRoleLogs($searchTerm, $perPage)
    {
        $query = \Spatie\Activitylog\Models\Activity::where('log_name', 'role')
            ->with('causer');

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', '%' . $searchTerm . '%') // Search តាមសកម្មភាព (Created, Updated...)
                
                // Search តាមឈ្មោះ Role (ដែលនៅក្នុង JSON properties)
                ->orWhere('properties->attributes->name', 'like', '%' . $searchTerm . '%')
                ->orWhere('properties->old->name', 'like', '%' . $searchTerm . '%')
                
                // Search តាមឈ្មោះ User ដែលជាអ្នកធ្វើ (Causer)
                ->orWhereHasMorph('causer', [\App\Models\User::class], function ($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        return $query->latest()->paginate($perPage === 'all' ? 100 : (int)$perPage);
    }

    /**
     * ទាញយកទិន្នន័យ Role ជាមួយ Search, Sort និង Pagination
     */
    public function getRoles($searchTerm, $perPage, $sortField, $sortDirection)
    {
        // ១. ទាញយក Level ធំបំផុតរបស់ User ដែលកំពុង Login (អនុគមន៍ដែលយើងបានបង្កើតពីមុន)
        $maxLevel = $this->getMaxAllowedLevelForCurrentUser();

        $query = Role::with('permissions')
            ->where('name', 'like', '%' . $searchTerm . '%')
            // ២. ត្រងយកតែ Role ណាដែលមាន Level តូចជាង ឬស្មើ Level របស់ User បច្ចុប្បន្ន
            ->where('level', '<=', $maxLevel) 
            ->orderBy($sortField, $sortDirection);

        if ($perPage === 'all') {
            // កែពី Role::count() មក $query->count() វិញទើបត្រឹមត្រូវតាមលក្ខខណ្ឌ
            return $query->paginate($query->count()); 
        }

        return $query->paginate((int)$perPage);
    }

    /**
     * បង្កើត ឬកែប្រែ Role និងផ្តល់ Permission
     */
    public function saveRole(array $data, $id = null)
    {
        $role = Role::updateOrCreate(
            ['id' => $id],
            [
                'name' => $data['name'],
                'level' => $data['level'],
                'guard_name' => $data['guard_name'] ?? 'web'
            ]
        );

        // Give permissions to role
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    /**
     * លុប Role
     */
    public function deleteRoles($ids) {
        $ids = is_array($ids) ? $ids : [$ids];
        // ត្រូវប្រើ find() រួច delete() ដើម្បីឱ្យ Model Event ដំណើរការ
        return Role::whereIn('id', $ids)->get()->each(function($role) {
            $role->delete(); 
        });
    }

    /**
     * ទាញយក Roles តាម IDs សម្រាប់ Bulk Edit
     */
    public function getRolesByIds(array $ids)
    {
        return Role::whereIn('id', $ids)->get();
    }

    /**
     * ទាញយក Permission ទាំងអស់សម្រាប់បង្ហាញក្នុង Form
     */
    public function getAllPermissions()
    {
        return Permission::all();
    }
}