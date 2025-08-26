<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\ExpenseCategory\Database\Seeders\ExpenseCategoryStatusSeeder;
use Modules\SiteMaster\Database\Seeders\SiteMasterStatusSeeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $this->call([
                // UserDatabaseSeeder::class,
                ExpenseCategoryStatusSeeder::class,
                SiteMasterStatusSeeder::class,          ]);
    }
}
