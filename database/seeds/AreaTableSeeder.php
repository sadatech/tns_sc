<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class AreaTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        foreach(range(1,20) as $i){
            DB::table('areas')->insert([
                'name'          => $faker->city,
                'id_region'     => rand(1, 4),
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            ]);
        }
    }
}
