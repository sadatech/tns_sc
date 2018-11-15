<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ChannelTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('channels')->insert([
            ['name' => 'GTC']
        ]);
        $faker = Faker::create();
        foreach(range(0,99) as $i){
            DB::table('channels')->insert([
                'name'          => $faker->firstName,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            ]);
        }
    }
}
