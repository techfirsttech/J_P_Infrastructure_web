<?php

namespace Modules\Currency\Database\Seeders;

use Illuminate\Database\Seeder;

use Modules\Currency\Models\Currency;

class CurrencyDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);
        Currency::create([
            'currency_name' => 'Indian Rupee',
            'currency_symbol' => '₹',
            'created_by' => 1,
            'updated_by' => 1,

        ]);
        Currency::create([
            'currency_name' => 'US Dollar',
            'currency_symbol' => '$',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
