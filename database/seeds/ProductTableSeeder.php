<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ProductTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        foreach(range(0,9) as $i){
            DB::table('products')->insert([
                'name'          => $faker->company,
                'deskripsi'     => $faker->catchPhrase,
                'id_category'   => rand(1, 10),
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            ]);
        }
    }
}
