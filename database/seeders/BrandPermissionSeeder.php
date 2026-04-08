<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BrandPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view-brand',
            'create-brand',
            'edit-brand',
            'delete-brand',
            'view-brand-logs',
            'delete-brand-logs',
            'view-brand-trash',
            'restore-brand',
            'force-delete-brand',
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