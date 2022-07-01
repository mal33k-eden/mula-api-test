<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KYCLevel extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('kyc_levels')->insert([
            'level' => '1',
            'trx_cap' => 10000,
        ]);
        DB::table('kyc_levels')->insert([
            'level' => '2',
            'trx_cap' => 100000,
        ]);
        DB::table('kyc_levels')->insert([
            'level' => '3',
            'trx_cap' => 1000000,
        ]);

    }
}
