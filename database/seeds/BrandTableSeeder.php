<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('brands')->insert([
            ['name' => 'SASA']
        ]);
    	$faker = Faker::create();
    	foreach(range(0,99) as $i){
    		DB::table('brands')->insert([
    			'name' => $faker->firstName,
    			'keterangan' => $faker->sentence(rand(6,10), true),
    			'created_at' => Carbon::now(),
    			'updated_at' => Carbon::now()
    		]);
    	}
    }
}
