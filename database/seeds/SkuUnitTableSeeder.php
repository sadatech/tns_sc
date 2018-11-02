<?php

use App\SkuUnit;
use Illuminate\Database\Seeder;

class SkuUnitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	for ($i=1; $i < 11; $i++) { 
    		SkuUnit::create([
    			'name' => 'Satuan ' . $i,
    			'conversion_value' => $i * 10,
    		]);
        }
    }
}
