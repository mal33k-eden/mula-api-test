<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KYCLevelRequirement extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::table('kyc_level_requirements')->insert([
            'kyc_level_id' => '1',
            'connected_country_id' => 1,
            'document' => 'Passport',
        ]);
        DB::table('kyc_level_requirements')->insert([
            'kyc_level_id' => '1',
            'connected_country_id' => 1,
            'document' => 'Valid Bill',
        ]);
    }
}
