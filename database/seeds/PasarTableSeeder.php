<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PasarTableSeeder extends Seeder
{

    public function run()
    {
        $faker = Faker::create();
        foreach(range(0,99) as $i){
            DB::table('pasars')->insert([
                'name'              => $faker->name,
                'address'           => $faker->address,
                'latitude'          => ''.rand(0000000,99999999),
                'longitude'         => ''.rand(0000000,99999999),
                'id_subarea'        => rand(1, 49),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ]);
        }
    }
}
