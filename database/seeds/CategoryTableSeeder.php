<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CategoryTableSeeder extends Seeder
{
    public function run()
    {
   
        // for ($i=1; $i < 11; $i++) { 
        //     DB::table('categories')->insert([
        //         'name'          => 'Category ' . $i,
        //         'created_at'    => Carbon::now(),
        //         'updated_at'    => Carbon::now()
        //     ]);
        // }

        $exists = Category::where('deleted_at',null)->count();
        if ($exists <= 0) {
            DB::table('categories')->insert([
                ['name' => 'MNG', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'COCONUT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'KALDU', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'SEASONING', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'CONDIMENT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'TEPUNG BUMBU', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ]);
        }
    }
}