<?php

namespace Modules\SiteMaster\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SiteMaster\Models\SiteMasterStatus;

class SiteMasterStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addStatus = [
            [
                'status_name' => 'Pending',
                'status' => 'active',
                'color_class' => 'warning',
                'created_at' => now(),
                'created_by' => 1
            ],
            [
                'status_name' => 'Approved',
                'status' => 'active',
                'color_class' => 'success',
                'created_at' => now(),
                'created_by' => 1
            ],
            [
                'status_name' => 'Rejected',
                'status' => 'active',
                'color_class' => 'danger',
                'created_at' => now(),
                'created_by' => 1
            ]
        ];

        // Fetch existing status_names
        $existingStatusNames = SiteMasterStatus::pluck('status_name')->toArray();

        // Filter out the ones that already exist
        $newStatuses = array_filter($addStatus, function ($status) use ($existingStatusNames) {
            return !in_array($status['status_name'], $existingStatusNames);
        });

        // Insert only new ones
        if (!empty($newStatuses)) {
            SiteMasterStatus::insert($newStatuses);
        }
    }
}
