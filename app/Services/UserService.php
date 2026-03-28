<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserService
{
    // ១. កែប្រែ Function ទាញយក Users
    public function getUsers($searchTerm, $perPage, $sortField, $sortDirection, $myMaxLevel)
    {
        $query = \App\Models\User::with('roles')
            // ច្រោះយកតែ User ណាដែល "គ្មាន" Role ណាដែលមាន Level ធំជាងខ្លួន 
            // មានន័យថា Admin (99) នឹងមិនអាចមើលឃើញ User ណាដែលមាន Role Level (999) ឡើយ
            ->whereDoesntHave('roles', function ($q) use ($myMaxLevel) {
                $q->where('level', '>', $myMaxLevel);
            })
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            })
            ->orderBy($sortField, $sortDirection);

        return $perPage === 'all' ? $query->paginate(\App\Models\User::count()) : $query->paginate((int)$perPage);
    }

    // ២. កែប្រែ Function Save User (ដក Level ចេញវិញ ព្រោះវានៅក្នុងតារាង roles ស្រាប់)
    public function saveUser(array $data, $id = null)
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => $data['status'] ?? true,
            // លែងត្រូវការបញ្ជូល level ទៅក្នុងតារាង users ទៀតហើយ
        ];

        if (!empty($data['password'])) {
            $userData['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        }

        $user = \App\Models\User::updateOrCreate(
            ['id' => $id], 
            $userData
        );

        if (!empty($data['role_id'])) {
            $user->roles()->sync([$data['role_id']]);
        } else {
            $user->roles()->detach();
        }

        return $user;
    }

    // ៣. កែប្រែ Function សម្រាប់ Trash ផងដែរ
    public function getTrashedUsers($searchTerm, $perPage, $myMaxLevel)
    {
        return \App\Models\User::onlyTrashed()
            // ការពារកុំឱ្យ Admin មើលឃើញគណនី Super Admin នៅក្នុងធុងសំរាម
            ->whereDoesntHave('roles', function ($q) use ($myMaxLevel) {
                $q->where('level', '>', $myMaxLevel);
            })
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->paginate((int)$perPage);
    }
    

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();
        return $user->status;
    }

    public function deleteUsers($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return User::whereIn('id', $ids)->get()->each->delete();
    }

    public function getAllRoles()
    {
        return \App\Models\Role::whereNull('deleted_at')->get();
    }

    /**
     * ស្តារ User ឡើងវិញពី Trash
     */
    public function restoreUsers($ids) {
        $ids = is_array($ids) ? $ids : [$ids];
        return User::onlyTrashed()->whereIn('id', $ids)->get()->each(function($user) {
            $user->restore(); 
        });
    }

    /**
     * លុប User ចោលទាំងស្រុងពី Database (Force Delete)
     */
    public function forceDeleteUsers($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return User::onlyTrashed()->whereIn('id', $ids)->forceDelete();
    }

    /**
     * ទាញយក Activity Logs របស់ User
     */
    public function getUserLogs($searchTerm, $perPage, $myMaxLevel)
    {
        $query = \Spatie\Activitylog\Models\Activity::where('log_name', 'user')
            ->with(['causer', 'subject']);

        // ១. ឆែកអ្នកធ្វើសកម្មភាព (Causer): ត្រូវតែមាន Level <= យើង ឬជា System (null)
        $query->where(function ($q) use ($myMaxLevel) {
            $q->whereHasMorph('causer', [\App\Models\User::class], function ($subQuery) use ($myMaxLevel) {
                // ប្រើ whereDoesntHave ដើម្បីប្រាកដថាគ្មាន Role ណាដែលមាន Level ធំជាងយើងឡើយ
                $subQuery->whereDoesntHave('roles', function ($roleQuery) use ($myMaxLevel) {
                    $roleQuery->where('level', '>', $myMaxLevel);
                });
            })
            ->orWhereNull('causer_id'); // បង្ហាញ Log ដែលកើតចេញពីប្រព័ន្ធ
        });

        // ២. ឆែកអ្នកដែលរងគ្រោះ (Subject): ត្រូវតែមាន Level <= យើង
        $query->where(function ($q) use ($myMaxLevel) {
            $q->whereHasMorph('subject', [\App\Models\User::class], function ($subQuery) use ($myMaxLevel) {
                $subQuery->whereDoesntHave('roles', function ($roleQuery) use ($myMaxLevel) {
                    $roleQuery->where('level', '>', $myMaxLevel);
                });
            })
            // ករណី Subject ត្រូវបាន Force Delete (បាត់ទិន្នន័យពី User Table) ក៏អនុញ្ញាតឱ្យបង្ហាញដែរ
            ->orWhereDoesntHaveMorph('subject', [\App\Models\User::class]); 
        });

        // ផ្នែក Search
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', '%' . $searchTerm . '%')
                ->orWhereHasMorph('causer', [\App\Models\User::class], function ($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('properties->attributes->name', 'like', '%' . $searchTerm . '%')
                ->orWhere('properties->old->name', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query->latest()->paginate($perPage === 'all' ? 100 : (int)$perPage);
    }
}