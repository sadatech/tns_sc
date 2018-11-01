<?php

use App\Product;
use App\ProductUnit;
use Illuminate\Database\Seeder;

class ProductUnitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	foreach (Product::all() as $product) {
    		ProductUnit::create([
    			'product_id' => $product->id,
    			'sku_unit_id' => rand(1, 10)
    		]);
    	}
    }
}
