<?php

use Illuminate\Database\Seeder;
use App\Employee;
use Carbon\Carbon;
use App\EmployeeStore;
use App\Target;

class TargetTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = Employee::get();

        // SALES HEADER       
    	foreach($employees as $i){

    		$store_id = EmployeeStore::where('id_employee', ($i->id))->pluck('id_store')->toArray();

    		foreach ($store_id as $j) {
    			$howMany = 20;
				if($i->position->level == 'spgmtc') $howMany = 50;

    			foreach(range(1, $howMany) as $k){
    				Target::create([
						'id_employee' => $i->id,
			            'id_store' => $j,
			            'id_product' => $k,
			            'quantity' => rand(1000, 10000),
			            'rilis' => '2018-11-01',
			            'created_at' => Carbon::now(),
			            'updated_at' => Carbon::now(),
					]);
    			}
    		}

    	}
    }
}
