<?php

namespace Modules\ExpenseCategory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ExpenseCategory\Models\ExpenseCategoryStatus;

class ExpenseCategoryStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addStatus = [
            [
                'expense_category_status_name' => 'Pending',
                'status' => 'active',
                'color_class' => 'warning',
                'created_at' => now(),
                'created_by' => 1
            ],
            [
                'expense_category_status_name' => 'Approved',
                'status' => 'active',
                'color_class' => 'success',
                'created_at' => now(),
                'created_by' => 1
            ],
            [
                'expense_category_status_name' => 'Rejected',
                'status' => 'active',
                'color_class' => 'danger',
                'created_at' => now(),
                'created_by' => 1
            ]
        ];

        // Fetch existing status_names
        $existingStatusNames = ExpenseCategoryStatus::pluck('expense_category_status_name')->toArray();

        // Filter out the ones that already exist
        $newStatuses = array_filter($addStatus, function ($status) use ($existingStatusNames) {
            return !in_array($status['expense_category_status_name'], $existingStatusNames);
        });

        // Insert only new ones
        if (!empty($newStatuses)) {
            ExpenseCategoryStatus::insert($newStatuses);
        }
    }
}
