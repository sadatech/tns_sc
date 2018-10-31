<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CategoryTableSeeder extends Seeder
{
    public function run()
    {
        for ($i=1; $i < 11; $i++) { 
            DB::table('categories')->insert([
                'name'          => 'Category ' . $i,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            ]);
        }
    }
}