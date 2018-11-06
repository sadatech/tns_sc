<?php

use App\Product;
use App\ProductMeasure;
use Illuminate\Database\Seeder;

class ProductMeasureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	foreach (Product::all() as $product) {
    		ProductMeasure::create([
    			'id_product' => $product->id,
    			'id_measure' => rand(1, 10),
    		]);
    	}
    }
}
