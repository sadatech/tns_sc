<?php

use App\Product;
use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    public function run()
    {
        for ($i=1; $i < 11; $i++) { 
            Product::create([
                'name' => 'Product ' . $i,
                'id_subcategory' => rand(1, 10),
                'id_brand' => 1,
                'code' => rand(10000, 100000),
                'stock_type_id' => rand(1, 2),
                'panel' => 'yes'
            ]);
        }
    }
}
