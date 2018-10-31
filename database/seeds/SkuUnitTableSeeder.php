<?php

use App\SkuUnit;
use App\MeasurementUnit;
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
    	// for ($i=1; $i < 11; $i++) { 
    	// 	SkuUnit::create([
    	// 		'name' => 'Satuan ' . $i,
    	// 		'conversion_value' => $i * 10
    	// 	]);
     //    }

        for ($i=1; $i < 11; $i++) { 
            MeasurementUnit::create([
                'name' => 'Satuan ' . $i,
                'size' => $i * 10
            ]);
        }
    }
}
