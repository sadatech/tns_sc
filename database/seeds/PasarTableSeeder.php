<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PasarTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach(range(1,20) as $i){
            DB::table('pasars')->insert([
                'name'          => $faker->firstName,
                'address'       => $faker->address,
                'latitude'      => $faker->latitude,
                'longitude'     => $faker->longitude,
                'id_sub_area'   => rand(1,50),
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            ]);
        }
    }
}
