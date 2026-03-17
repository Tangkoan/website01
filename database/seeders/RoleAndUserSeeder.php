<?php

namespace Database\Seeders; // <--- ត្រូវថែមបន្ទាត់នេះដាច់ខាត

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // ប្រើ firstOrCreate ដើម្បីការពារកុំឱ្យទើស Error បើវាមានរួចហើយ
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'view logs']);

        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'level' => 999 
        ]);

        $user = User::firstOrCreate(
            ['email' => 'tg@gmail.com'], // បើមាន Email នេះហើយ វាមិនបង្កើតថ្មីទេ
            [
                'name' => 'tg',
                'password' => bcrypt('Tangkoan@1100'),
            ]
        );

        $user->assignRole($superAdminRole);
    }

    
}