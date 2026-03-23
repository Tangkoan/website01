<?php

namespace App\Services;

use App\Models\Permission;

class PermissionService
{
    /**
     * ទាញយកទិន្នន័យ Permission ជាមួយ Search, Sort និង Pagination
     */
    public function getPermissions($searchTerm, $perPage, $sortField, $sortDirection)
    {
        $query = Permission::where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortField, $sortDirection);

        if ($perPage === 'all') {
            return $query->paginate(Permission::count());
        }

        return $query->paginate((int)$perPage);
    }

    /**
     * បង្កើត ឬកែប្រែ Permission (Single)
     */
    public function savePermission(array $data, $id = null)
    {
        return Permission::updateOrCreate(
            ['id' => $id],
            [
                'name' => $data['name'],
                'guard_name' => $data['guard_name'] ?? 'web'
            ]
        );
    }

    /**
     * លុប Permission (ម្នាក់ឯង ឬ លុបច្រើនក្នុងពេលតែមួយ)
     */
    public function deletePermissions($ids)
    {
        // បំប្លែងឱ្យទៅជា Array ទោះជាបញ្ជូនមកតែ ID មួយក៏ដោយ
        $ids = is_array($ids) ? $ids : [$ids];
        return Permission::whereIn('id', $ids)->delete();
    }

    /**
     * ទាញយកទិន្នន័យសម្រាប់ធ្វើ Bulk Edit Queue
     */
    public function getPermissionsByIds(array $ids)
    {
        return Permission::whereIn('id', $ids)->get(['id', 'name', 'guard_name']);
    }
}