<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class StoreTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        foreach(range(0,99) as $i){
            DB::table('stores')->insert([
                'name1'             => $faker->name,
                'name2'             => $faker->name,
                'address'           => $faker->address,
                'latitude'          => $faker->latitude(-90, 90),
                'longitude'         => $faker->longitude(-180, 180),
                'id_account'        => rand(1, 99),
                'id_subarea'        => rand(1, 49),
                'id_timezone'       => rand(1, 3),
                'id_salestier'      => rand(1, 3),
                'is_vito'           => "default is_vito",
                'store_panel'       => "default store_panel",
                'coverage'          => "default coverage",
                'delivery'          => "default delivery",
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ]);
            DB::table('store_gtcs')->insert([
                'name'              => $faker->name,
                'phone'             => '0857'.rand(0000000,99999999),
                'id_pasar'          => rand(1, 99),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ]);
        }
    }
}
