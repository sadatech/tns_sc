<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PlaceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$faker = Faker::create();
    	foreach(range(0,99) as $i){
    		DB::table('places')->insert([
    			'name' => $faker->name,
    			'code' => $faker->bothify('TR?#?#??##'),
    			'phone' => '0857'.rand(0000000,99999999),
                'email' => $faker->email,
    			'latitude' => $faker->latitude(-90, 90),
    			'longitude' => $faker->longitude(-90, 90),
    			'address' => $faker->address,
    			'description' => $faker->sentence(rand(6,10), true),
    			'created_at' => Carbon::now(),
    			'updated_at' => Carbon::now()
    		]);
    	}
    }
}
