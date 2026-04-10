<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sidebar;

class SidebarSeeder extends Seeder
{
    public function run(): void
    {
        // បញ្ជី Menu ដើម (Default Menus) ដែលត្រូវបញ្ចូលទៅក្នុង Database
        $menus = [
            [
                'title' => 'home', 
                'url' => 'dashboard', 
                'icon' => '🏠', 
                'permission' => 'view_dashboard', 
                'order' => 10
            ],
            [
                'title' => 'users_roles', 
                'url' => null, 
                'icon' => '👥', 
                'permission' => null, 
                'order' => 20,
                'children' => [
                    ['title' => 'users', 'url' => 'settings/users', 'permission' => 'view_users', 'order' => 1],
                    ['title' => 'roles', 'url' => 'settings/roles', 'permission' => 'view_roles', 'order' => 2],
                    ['title' => 'permission', 'url' => 'settings/permission', 'permission' => 'view_permissions', 'order' => 3],
                    ['title' => 'role_ui_mode', 'url' => 'settings/role-ui', 'permission' => 'manage_role_ui', 'order' => 4],
                ]
            ],
            [
                'title' => 'settings', 
                'url' => null, 
                'icon' => '⚙️', 
                'permission' => null, 
                'order' => 30,
                'children' => [
                    ['title' => 'shop_info', 'url' => 'settings/shop', 'permission' => 'view_shop_info', 'order' => 1],
                    ['title' => 'theme', 'url' => 'settings/theme', 'permission' => 'view_theme', 'order' => 2],
                    ['title' => 'system_configs', 'url' => 'settings/configs', 'permission' => 'manage_system_configs', 'order' => 3],
                    ['title' => 'activity_logs', 'url' => 'settings/action', 'permission' => 'view-activity-logs', 'order' => 4],
                    ['title' => 'recycle_bin', 'url' => 'settings/recycle', 'permission' => null, 'order' => 5],
                ]
            ]
        ];

        // ដំណើរការបញ្ចូលទៅក្នុង Database
        foreach ($menus as $menuData) {
            $children = $menuData['children'] ?? [];
            unset($menuData['children']);

            // ប្រើ updateOrCreate ដើម្បីកុំឱ្យជាន់គ្នាពេល Run ច្រើនដង
            $parent = Sidebar::updateOrCreate(
                ['title' => $menuData['title']], // លក្ខខណ្ឌផ្ទៀងផ្ទាត់
                $menuData
            );

            // បញ្ចូល Menu កូនៗ
            foreach ($children as $childData) {
                Sidebar::updateOrCreate(
                    ['url' => $childData['url']], // លក្ខខណ្ឌផ្ទៀងផ្ទាត់កូន
                    array_merge($childData, ['parent_id' => $parent->id])
                );
            }
        }

        $this->command->info('✅ Sidebar Menus seeded successfully!');
    }
}