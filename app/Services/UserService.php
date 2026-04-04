<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getUsers($searchTerm, $perPage, $sortField, $sortDirection, $myMaxLevel)
    {
        $query = User::with('roles')
            ->whereDoesntHave('roles', function ($q) use ($myMaxLevel) {
                $q->where('level', '>', $myMaxLevel);
            })
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            })
            ->orderBy($sortField, $sortDirection);

        // ✅ កែប្រែ៖ ប្រើ $query->count() ជំនួស User::count() ដើម្បីកុំឱ្យខាត Query ពេលរើស 'all'
        return $perPage === 'all' ? $query->paginate($query->count()) : $query->paginate((int)$perPage);
    }

    public function saveUser(array $data, $id = null)
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => $data['status'] ?? true,
        ];

        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $user = User::updateOrCreate(['id' => $id], $userData);

        if (!empty($data['role_id'])) {
            $user->roles()->sync([$data['role_id']]);
        } else {
            $user->roles()->detach();
        }

        return $user;
    }

    public function getTrashedUsers($searchTerm, $perPage, $myMaxLevel)
    {
        return User::onlyTrashed()
            ->with('roles') // ✅ កែប្រែ៖ បន្ថែម with('roles') ការពារ N+1 Query ក្នុងធុងសំរាម
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
        return Role::whereNull('deleted_at')->get();
    }

    public function restoreUsers($ids) {
        $ids = is_array($ids) ? $ids : [$ids];
        return User::onlyTrashed()->whereIn('id', $ids)->get()->each(function($user) {
            $user->restore(); 
        });
    }

    public function forceDeleteUsers($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        // ✅ កែប្រែ៖ ប្រើ get()->each() ដើម្បីបញ្ឆេះ Model Events ឱ្យ Activity Log ចាប់បាន
        return User::onlyTrashed()->whereIn('id', $ids)->get()->each(function($user) {
            $user->forceDelete();
        });
    }

    public function getUserLogs($searchTerm, $perPage, $myMaxLevel)
    {
        $query = \Spatie\Activitylog\Models\Activity::where('log_name', 'user')
            ->with(['causer' => function($q) {
                $q->withTrashed(); 
            }, 'subject' => function($q) {
                $q->withTrashed(); 
            }]);

        $query->where(function ($q) use ($myMaxLevel) {
            $q->whereNull('causer_id')
              ->orWhereHasMorph('causer', [User::class], function ($subQuery) use ($myMaxLevel) {
                  $subQuery->withTrashed()->whereDoesntHave('roles', function ($roleQuery) use ($myMaxLevel) {
                      $roleQuery->where('level', '>', $myMaxLevel);
                  });
              })
              ->orWhereDoesntHaveMorph('causer', [User::class]); 
        });

        $query->where(function ($q) use ($myMaxLevel) {
            $q->whereNull('subject_id')
              ->orWhereHasMorph('subject', [User::class], function ($subQuery) use ($myMaxLevel) {
                  $subQuery->withTrashed()->whereDoesntHave('roles', function ($roleQuery) use ($myMaxLevel) {
                      $roleQuery->where('level', '>', $myMaxLevel);
                  });
              })
              ->orWhereDoesntHaveMorph('subject', [User::class]); 
        });

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', '%' . $searchTerm . '%')
                  ->orWhereHasMorph('causer', [User::class], function ($subQuery) use ($searchTerm) {
                      $subQuery->withTrashed()->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhere('properties->attributes->name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('properties->old->name', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query->latest()->paginate($perPage === 'all' ? 100 : (int)$perPage);
    }
}