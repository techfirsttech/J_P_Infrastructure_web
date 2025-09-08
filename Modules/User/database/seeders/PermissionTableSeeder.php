<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['title_tag' => 'Role', 'title' => 'List', 'name' => 'role-list'],
            ['title_tag' => 'Role', 'title' => 'Create', 'name' => 'role-create'],
            ['title_tag' => 'Role', 'title' => 'Edit', 'name' => 'role-edit'],
            ['title_tag' => 'Role', 'title' => 'Delete', 'name' => 'role-delete'],

            ['title_tag' => 'User', 'title' => 'List', 'name' => 'users-list'],
            ['title_tag' => 'User', 'title' => 'Create', 'name' => 'users-create'],
            ['title_tag' => 'User', 'title' => 'Edit', 'name' => 'users-edit'],
            ['title_tag' => 'User', 'title' => 'Delete', 'name' => 'users-delete'],

            ['title_tag' => 'User_Assign', 'title' => 'List', 'name' => 'assign-user-list'],
            ['title_tag' => 'User_Assign', 'title' => 'Create', 'name' => 'assign-user-create'],

            ['title_tag' => 'Password_Change', 'name' => 'password-change', 'title' => 'Change'],

            ['title_tag' => 'Setting', 'title' => 'Create', 'name' => 'setting'],

            ['title_tag' => 'Menu', 'title' => 'List', 'name' => 'menu-list'],
            ['title_tag' => 'Menu', 'title' => 'Create', 'name' => 'menu-create'],
            ['title_tag' => 'Menu', 'title' => 'Edit', 'name' => 'menu-edit'],
            ['title_tag' => 'Menu', 'title' => 'Delete', 'name' => 'menu-delete'],

            ['title_tag' => 'Country', 'title' => 'List', 'name' => 'country-list'],
            ['title_tag' => 'Country', 'title' => 'Create', 'name' => 'country-create'],
            ['title_tag' => 'Country', 'title' => 'Edit', 'name' => 'country-edit'],
            ['title_tag' => 'Country', 'title' => 'Delete', 'name' => 'country-delete'],

            ['title_tag' => 'State', 'title' => 'List', 'name' => 'state-list'],
            ['title_tag' => 'State', 'title' => 'Create', 'name' => 'state-create'],
            ['title_tag' => 'State', 'title' => 'Edit', 'name' => 'state-edit'],
            ['title_tag' => 'State', 'title' => 'Delete', 'name' => 'state-delete'],

            ['title_tag' => 'City', 'title' => 'List', 'name' => 'city-list'],
            ['title_tag' => 'City', 'title' => 'Create', 'name' => 'city-create'],
            ['title_tag' => 'City', 'title' => 'Edit', 'name' => 'city-edit'],
            ['title_tag' => 'City', 'title' => 'Delete', 'name' => 'city-delete'],

            ['title_tag' => 'Unit', 'title' => 'List', 'name' => 'unit-list'],
            ['title_tag' => 'Unit', 'title' => 'Create', 'name' => 'unit-create'],
            ['title_tag' => 'Unit', 'title' => 'Edit', 'name' => 'unit-edit'],
            ['title_tag' => 'Unit', 'title' => 'Delete', 'name' => 'unit-delete'],

            ['title_tag' => 'Year', 'name' => 'year-list', 'title' => 'List'],
            ['title_tag' => 'Year', 'name' => 'year-create', 'title' => 'Create'],
            ['title_tag' => 'Year', 'name' => 'year-edit', 'title' => 'Edit'],
            ['title_tag' => 'Year', 'name' => 'year-delete', 'title' => 'Delete'],

            ['title_tag' => 'Currency', 'name' => 'currency-list', 'title' => 'List'],
            ['title_tag' => 'Currency', 'name' => 'currency-create', 'title' => 'Create'],
            ['title_tag' => 'Currency', 'name' => 'currency-edit', 'title' => 'Edit'],
            ['title_tag' => 'Currency', 'name' => 'currency-delete', 'title' => 'Delete'],

            ['title_tag' => 'Category', 'name' => 'category-list', 'title' => 'List'],
            ['title_tag' => 'Category', 'name' => 'category-create', 'title' => 'Create'],
            ['title_tag' => 'Category', 'name' => 'category-edit', 'title' => 'Edit'],
            ['title_tag' => 'Category', 'name' => 'category-delete', 'title' => 'Delete'],

            ['title_tag' => 'Expense_Category', 'name' => 'expense-category-list', 'title' => 'List'],
            ['title_tag' => 'Expense_Category', 'name' => 'expense-category-create', 'title' => 'Create'],
            ['title_tag' => 'Expense_Category', 'name' => 'expense-category-edit', 'title' => 'Edit'],
            ['title_tag' => 'Expense_Category', 'name' => 'expense-category-delete', 'title' => 'Delete'],

            ['title_tag' => 'Site_Master', 'name' => 'site-master-list', 'title' => 'List'],
            ['title_tag' => 'Site_Master', 'name' => 'site-master-create', 'title' => 'Create'],
            ['title_tag' => 'Site_Master', 'name' => 'site-master-edit', 'title' => 'Edit'],
            ['title_tag' => 'Site_Master', 'name' => 'site-master-delete', 'title' => 'Delete'],

            ['title_tag' => 'Expense_Master', 'name' => 'expense-master-list', 'title' => 'List'],
            ['title_tag' => 'Expense_Master', 'name' => 'expense-master-create', 'title' => 'Create'],
            ['title_tag' => 'Expense_Master', 'name' => 'expense-master-edit', 'title' => 'Edit'],
            ['title_tag' => 'Expense_Master', 'name' => 'expense-master-delete', 'title' => 'Delete'],

            ['title_tag' => 'Income_Master', 'name' => 'income-master-list', 'title' => 'List'],
            ['title_tag' => 'Income_Master', 'name' => 'income-master-create', 'title' => 'Create'],
            ['title_tag' => 'Income_Master', 'name' => 'income-master-edit', 'title' => 'Edit'],
            ['title_tag' => 'Income_Master', 'name' => 'income-master-delete', 'title' => 'Delete'],

            ['title_tag' => 'Payment_Master', 'name' => 'payment-master-list', 'title' => 'List'],
            ['title_tag' => 'Payment_Master', 'name' => 'payment-master-create', 'title' => 'Create'],
            ['title_tag' => 'Payment_Master', 'name' => 'payment-master-edit', 'title' => 'Edit'],
            ['title_tag' => 'Payment_Master', 'name' => 'payment-master-delete', 'title' => 'Delete'],

            ['title_tag' => 'Raw_Material_Category', 'name' => 'material-category-list', 'title' => 'List'],
            ['title_tag' => 'Raw_Material_Category', 'name' => 'material-category-create', 'title' => 'Create'],
            ['title_tag' => 'Raw_Material_Category', 'name' => 'material-category-edit', 'title' => 'Edit'],
            ['title_tag' => 'Raw_Material_Category', 'name' => 'material-category-delete', 'title' => 'Delete'],

            ['title_tag' => 'Raw_Material_Master', 'name' => 'material-master-list', 'title' => 'List'],
            ['title_tag' => 'Raw_Material_Master', 'name' => 'material-master-create', 'title' => 'Create'],
            ['title_tag' => 'Raw_Material_Master', 'name' => 'material-master-edit', 'title' => 'Edit'],
            ['title_tag' => 'Raw_Material_Master', 'name' => 'material-master-delete', 'title' => 'Delete'],

            ['title_tag' => 'Supplier', 'name' => 'supplier-list', 'title' => 'List'],
            ['title_tag' => 'Supplier', 'name' => 'supplier-create', 'title' => 'Create'],
            ['title_tag' => 'Supplier', 'name' => 'supplier-edit', 'title' => 'Edit'],
            ['title_tag' => 'Supplier', 'name' => 'supplier-delete', 'title' => 'Delete'],

            ['title_tag' => 'Labour', 'name' => 'labour-list', 'title' => 'List'],
            ['title_tag' => 'Labour', 'name' => 'labour-create', 'title' => 'Create'],
            ['title_tag' => 'Labour', 'name' => 'labour-edit', 'title' => 'Edit'],
            ['title_tag' => 'Labour', 'name' => 'labour-delete', 'title' => 'Delete'],

            ['title_tag' => 'Attendance', 'name' => 'attendance-list', 'title' => 'List'],
            ['title_tag' => 'Attendance', 'name' => 'attendance-create', 'title' => 'Create'],
            ['title_tag' => 'Attendance', 'name' => 'attendance-edit', 'title' => 'Edit'],
            ['title_tag' => 'Attendance', 'name' => 'attendance-delete', 'title' => 'Delete'],

            ['title_tag' => 'Contractor', 'name' => 'contractor-list', 'title' => 'List'],
            ['title_tag' => 'Contractor', 'name' => 'contractor-create', 'title' => 'Create'],
            ['title_tag' => 'Contractor', 'name' => 'contractor-edit', 'title' => 'Edit'],
            ['title_tag' => 'Contractor', 'name' => 'contractor-delete', 'title' => 'Delete'],

            ['title_tag' => 'StockTransfer', 'name' => 'stock-transfer-list', 'title' => 'List'],
            ['title_tag' => 'StockTransfer', 'name' => 'stock-transfer-create', 'title' => 'Create'],
            ['title_tag' => 'StockTransfer', 'name' => 'stock-transfer-edit', 'title' => 'Edit'],
            ['title_tag' => 'StockTransfer', 'name' => 'stock-transfer-delete', 'title' => 'Delete'],

            ['title_tag' => 'Payment_Master', 'name' => 'payment-ledger-list', 'title' => 'Ledger'],

            ['title_tag' => 'Payment_Transfer', 'name' => 'payment-transfer-list', 'title' => 'List'],
            ['title_tag' => 'Payment_Transfer', 'name' => 'payment-transfer-create', 'title' => 'Create'],

            ['title_tag' => 'Party', 'name' => 'party-list', 'title' => 'List'],
            ['title_tag' => 'Party', 'name' => 'party-create', 'title' => 'Create'],
            ['title_tag' => 'Party', 'name' => 'party-edit', 'title' => 'Edit'],
            ['title_tag' => 'Party', 'name' => 'party-delete', 'title' => 'Delete'],

        ];

        foreach ($permissions as $permissionData) {
            $existingPermission = Permission::where('name', $permissionData['name'])->first();
            if (!$existingPermission) {
                Permission::create([
                    'name' => $permissionData['name'],
                    'title' => $permissionData['title'],
                    'title_tag' => $permissionData['title_tag']
                ]);
            }
        }
    }
}
