<?php

use Illuminate\Database\Seeder;
use App\Price;
use Carbon\Carbon;

class PriceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1; $i < 51; $i++) { 
            Price::create([
                'id_product' => $i,
                'price' => rand(10000, 100000),
                'rilis' => Carbon::parse('2018-11-01')
            ]);
        }

        for ($i=1; $i < 51; $i++) { 
            Price::create([
                'id_product' => $i,
                'price' => rand(10000, 100000),
                'rilis' => Carbon::parse('2018-11-15')
            ]);
        }

        for ($i=1; $i < 51; $i++) { 
            Price::create([
                'id_product' => $i,
                'price' => rand(10000, 100000),
                'rilis' => Carbon::parse('2018-11-25')
            ]);
        }
    }
}
