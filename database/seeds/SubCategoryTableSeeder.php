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
    	for ($i=1; $i < 11; $i++) { 
    		SubCategory::create([
    			'name' => 'Sub Category ' . $i,
    			'id_category' => rand(1, 10),
    		]);
        }
    }
}
