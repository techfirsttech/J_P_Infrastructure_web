<?php

namespace Modules\Year\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Year\Models\Year;
use Illuminate\Support\Facades\DB;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

class YearDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);

        $startYear = date('Y');
        $endYear = $startYear + 50;
        $insertYears = [];

        $createdUpdateBy = 0;
        $createdUpdateByResult = DB::table('users')->where('username', "super_admin")->first();
        if ($createdUpdateByResult) {
            $createdUpdateBy = $createdUpdateByResult->id;
        } else {
            UserDatabaseSeeder::class;
            $createdUpdateByResult = DB::table('users')->where('username', "super_admin")->first();
            if ($createdUpdateByResult) {
                $createdUpdateBy = $createdUpdateByResult->id;
            }
        }

        $existingCount = DB::table('years')->count();

        if ($existingCount > 0) {

            if ($this->command->confirm("Do you want to truncate the years table first?")) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::table('years')->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        }


        for ($year = $startYear; $year <= $endYear; $year++) {
            $nextYear = $year + 1;
            $nextYearShort = substr($nextYear, -2); // Get last 2 digits


            $set_default = ($year == $startYear) ? 1 : 0;
            $insertYears[] = array(
                'name' => $year . '-' . $nextYearShort,
                'set_default' => $set_default,
                'created_by' => $createdUpdateBy,
                'created_at' => now(),
                'updated_by' => $createdUpdateBy,
                'updated_at' => now(),
            );
        }

        DB::transaction(function () use ($insertYears) {
            DB::table('years')->insert($insertYears);
        });
    }
}
