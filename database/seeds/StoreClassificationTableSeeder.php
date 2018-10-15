<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class StoreClassificationTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        foreach(range(0,99) as $i){
            DB::table('classifications')->insert([
                'name'          => $faker->firstName,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            ]);
        }
    }
}
