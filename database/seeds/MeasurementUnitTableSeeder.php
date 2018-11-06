<?php

use App\MeasurementUnit;
use Illuminate\Database\Seeder;

class MeasurementUnitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	for ($i=1; $i < 11; $i++) { 
    		MeasurementUnit::create([
    			'name' => 'Satuan ' . $i,
    			'size' => $i * 10,
    		]);
        }
    }
}
