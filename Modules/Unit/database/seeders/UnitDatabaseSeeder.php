<?php

namespace Modules\Unit\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Unit\Models\Unit;
use Modules\Unit\Models\UnitGravity;

class UnitDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitGm = Unit::create([
            'name' => 'GM',
            'unit_value' => '1',
            'created_by' => 1,
            'updated_by' => 1,

        ]);
        $unitKg = Unit::create([
            'name' => 'KG',
            'unit_value' => '1',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        UnitGravity::create([
            'unit_id' => $unitKg->id,
            'child_id' => $unitGm->id,
            'unit_value' => '1000',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $unitLtr =  Unit::create([
            'name' => 'Ltr',
            'unit_value' => '1',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        UnitGravity::create([
            'unit_id' => $unitLtr->id,
            'child_id' => $unitKg->id,
            'unit_value' => '1',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        UnitGravity::create([
            'unit_id' => $unitLtr->id,
            'child_id' => $unitGm->id,
            'unit_value' => '1000',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
