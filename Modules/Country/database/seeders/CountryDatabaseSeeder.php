<?php

namespace Modules\Country\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Country\Models\Country;
use Modules\Country\Database\Seeders\CountryCityStateSeeder;

class CountryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CountryCityStateSeeder::class
        ]);
    }
}
