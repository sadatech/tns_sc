<?php

use App\Product;
use App\ProductUnit;
use App\ProductMeasure;
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
    	// foreach (Product::all() as $product) {
    	// 	ProductUnit::create([
    	// 		'product_id' => $product->id,
    	// 		'sku_unit_id' => rand(1, 10)
    	// 	]);
    	// }
        foreach (Product::all() as $product) {
            ProductMeasure::create([
                'id_product' => $product->id,
                'id_measure' => 1
            ]);
        }

        foreach (Product::all() as $product) {
            ProductMeasure::create([
                'id_product' => $product->id,
                'id_measure' => 2
            ]);
        }

        foreach (Product::all() as $product) {
            ProductMeasure::create([
                'id_product' => $product->id,
                'id_measure' => 3
            ]);
        }
    }
}
