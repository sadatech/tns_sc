<?php

use App\SubCategory;
use Illuminate\Database\Seeder;

class SubCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// for ($i=1; $i < 11; $i++) { 
    	// 	SubCategory::create([
    	// 		'name' => 'Sub Category ' . $i,
    	// 		'id_category' => rand(1, 10),
    	// 	]);
     //    }

        $exists = SubCategory::where('deleted_at',null)->count();
        if ($exists <= 0) {
            DB::table('sub_categories')->insert([
                ['name' => 'MNG', 'id_category' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'CCP', 'id_category' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'CCL', 'id_category' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'KALDU', 'id_category' => '3', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'DRY SEASONING', 'id_category' => '4', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'CHILLI', 'id_category' => '5', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'TOMAT', 'id_category' => '5', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'SAMBAL TERASI', 'id_category' => '5', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'TBS', 'id_category' => '6', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'TBAK', 'id_category' => '6', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'TBW', 'id_category' => '6', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'TGP', 'id_category' => '6', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'TBSF', 'id_category' => '6', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'TEPUNG', 'id_category' => '6', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ]);
        }
    }
}
