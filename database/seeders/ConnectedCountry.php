<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConnectedCountry extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('connected_countries')->insert([
            'name' => 'Ghana',
            'iso_code' => 'GH',
            'dial_code' => '233',
            'currency' => 'GHS',
        ]);
        DB::table('connected_countries')->insert([
            'name' => 'Nigeria',
            'iso_code' => 'NG',
            'dial_code' => '234',
            'currency' => 'NGN',
        ]);
        DB::table('connected_countries')->insert([
            'name' => 'United Kingdom',
            'iso_code' => 'UK',
            'dial_code' => '44',
            'currency' => 'GBP',
        ]);
    }
}
