<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CategoryPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view-category',
            'create-category',
            'edit-category',
            'delete-category',
            'view-category-logs',
            'delete-category-logs',
            'view-category-trash',
            'restore-category',
            'force-delete-category',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }
    }
}