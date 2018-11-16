<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class AccountTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        foreach(range(1,20) as $i){
            DB::table('accounts')->insert([
                'name'          => $faker->firstName,
                'id_channel'    => rand(1, 10),
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            ]);
        }
    }
}
