<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class SalesTierTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('sales_tiers')->insert([
            ['name' => 'Tier-1'],
            ['name' => 'Tier-2'],
            ['name' => 'Tier-3'],
            ['name' => 'Non Tier'],
        ]);
    }
}
