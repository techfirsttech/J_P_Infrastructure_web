<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);

        $countryId = NULL;
        $stateId = NULL;
        $cityId = NULL;


        $countryResult = DB::table('countries')->where('code', "IN")->first();
        if ($countryResult) {
            $countryId = $countryResult->id;
        }

        $stateResult = DB::table('states')->where('code', "GJ")->where('country_id', $countryId)->first();
        if ($stateResult) {
            $stateId = $stateResult->id;
        }

        $cityResult = DB::table('cities')->where('name', "Rajkot")->where('country_id', $countryId)->where('state_id', $stateId)->first();
        if ($cityResult) {
            $cityId  = $cityResult->id;
        }

        Setting::create([
            'company_name' => 'Sample Company',
            'mobile' => '9876543210',
            'email' => 'samplecompany@samplecompany.com',
            'address' => 'Sample Address',
            'tag_line' => 'Sample Tagline',
            'gst_number' => "24AAACC1206D1ZM",
            'pancard_number' => "AAACC1206D",
            'tan_number' => "AAACC1206D",
            'country_id' => $countryId,
            'state_id' => $stateId,
            'city_id' => $cityId,
        ]);
    }
}
