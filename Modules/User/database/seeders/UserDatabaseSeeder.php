<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;

use Modules\Country\Database\Seeders\CountryCityStateSeeder;
use Modules\Setting\Database\Seeders\SettingDatabaseSeeder;
use Modules\Unit\Database\Seeders\UnitDatabaseSeeder;
use Modules\Year\Database\Seeders\YearDatabaseSeeder;
use Modules\Currency\Database\Seeders\CurrencyDatabaseSeeder;
use Modules\MenuMaster\Database\Seeders\MenuMasterDatabaseSeeder;


class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for the User module.
     */
    public function run(): void
    {
        $this->call([
            PermissionTableSeeder::class,
            CreateAdminUserSeeder::class,
            CountryCityStateSeeder::class,
            SettingDatabaseSeeder::class,
            YearDatabaseSeeder::class,
            UnitDatabaseSeeder::class,
            CurrencyDatabaseSeeder::class,
            MenuMasterDatabaseSeeder::class,
        ]);
    }
}