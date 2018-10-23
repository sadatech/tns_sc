<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class StoreTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $store_type = array('MR', 'TR');
        foreach(range(0,99) as $i){
            DB::table('stores')->insert([
                'name1'             => $faker->name,
                'name2'             => $faker->name,
                'type'              => $store_type[rand(0,1)],
                'address'           => $faker->address,
                'latitude'          => $faker->latitude(-90, 90),
                'longitude'         => $faker->longitude(-180, 180),
                'id_account'        => rand(1, 99),
                'id_subarea'        => rand(1, 99),
                'photo'             => "default.png",
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ]);
        }
    }
}
