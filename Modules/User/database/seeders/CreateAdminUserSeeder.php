<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\User;
use Modules\User\Models\UserProfile;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $existingCount = DB::table('users')->count();

        if ($existingCount > 0) {

            if ($this->command->confirm("Do you want to truncate the users table first?")) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::table('users')->truncate();
                DB::table('roles')->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        }

        $user = User::create([
            'name' => 'Super Admin',
            'mobile' => '9876543210',
            'username' => 'super_admin',
            'email' => 'super@adm.com',
            'password' => bcrypt('Tech@#311'),
            'status' => "Active"
        ]);

        $role = Role::create(['name' => 'Super Admin']);
        $permissions = Permission::pluck('id')->all();
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);

        $companyUserRole = Role::create(['name' => 'Company Admin', 'title' => '']);


        $companyUser = User::create([
            'name' => 'Company Admin',
            'mobile' => '9876543110',
            'username' => 'company_admin',
            'email' => 'company_admin@adm.com',
            'password' => bcrypt('Company@#311'),
            'status' => "Active"
        ]);
        $permissions = Permission::where('title_tag', '!=', 'Menu')->pluck('id', 'id')->all();
        $companyUserRole->syncPermissions($permissions);
        $companyUser->assignRole([$companyUserRole->id]);


        $role = Role::create(['name' => 'Manager']);
        $role = Role::create(['name' => 'Site Manager']);
        $role = Role::create(['name' => 'Accountant']);
        $role = Role::create(['name' => 'Finance Manager']);
    }
}
