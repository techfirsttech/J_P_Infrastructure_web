<?php

namespace Modules\MenuMaster\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuMasterDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Insert Dashboard menu
        $dashboardId = DB::table('menu_masters')->insertGetId([
            'menu_icon' => 'icon-base ti tabler-smart-home',
            'menu_title' => 'message.dashboard',
            'menu_route' => 'dashboard',
            'module_name' => 'dashboard',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);



        // Insert General Master menu
        $generalMasterId = DB::table('menu_masters')->insertGetId([
            'menu_icon' => 'icon-base ti tabler-brand-google',
            'menu_title' => 'message.general_master',
            'menu_route' => 'javascript:void(0)',
            'module_name' => null,
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // General Master submenus
        $generalMasterSubmenus = [
            [
                'menu_icon' => 'icon-base ti tabler-settings',
                'menu_title' => 'setting::message.setting',
                'menu_route' => 'setting.index',
                'module_name' => 'setting'
            ],
            [
                'menu_icon' => 'icon-base ti tabler-globe',
                'menu_title' => 'country::message.country',
                'menu_route' => 'country.index',
                'module_name' => 'country'
            ],
            [
                'menu_icon' => 'icon-base ti tabler-square-rounded-letter-s',
                'menu_title' => 'state::message.state',
                'menu_route' => 'state.index',
                'module_name' => 'state'
            ],
            [
                'menu_icon' => 'icon-base ti tabler-map-pin',
                'menu_title' => 'city::message.city',
                'menu_route' => 'city.index',
                'module_name' => 'city'
            ],
            [
                'menu_icon' => 'icon-base ti tabler-scale',
                'menu_title' => 'unit::message.unit',
                'menu_route' => 'unit.index',
                'module_name' => 'unit'
            ],
            [
                'menu_icon' => 'icon-base ti tabler-world-dollar',
                'menu_title' => 'currency::message.currency_master',
                'menu_route' => 'currency.index',
                'module_name' => 'currency'
            ],
        ];

        // Insert General Master submenus
        foreach ($generalMasterSubmenus as $submenu) {
            DB::table('menu_masters')->insert(array_merge($submenu, [
                'parent_id' => $generalMasterId,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }



        // Insert User Management menu
        $userManagementId = DB::table('menu_masters')->insertGetId([
            'menu_icon' => 'icon-base ti tabler-users',
            'menu_title' => 'user::message.users',
            'menu_route' => 'javascript:void(0)',
            'module_name' => 'user',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // User Management submenus
        $userSubmenus = [
            [
                'menu_icon' => 'icon-base ti tabler-lock',
                'menu_title' => 'role::message.roles',
                'menu_route' => 'roles.index',
                'module_name' => 'role'
            ],
            [
                'menu_icon' => 'icon-base ti tabler-user-plus',
                'menu_title' => 'user::message.users',
                'menu_route' => 'users.index',
                'module_name' => 'user'
            ],

        ];
        // Insert User Management submenus
        foreach ($userSubmenus as $submenu) {
            DB::table('menu_masters')->insert(array_merge($submenu, [
                'parent_id' => $userManagementId,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}
