<?php

use Illuminate\Database\Seeder;
use App\EmployeeStore;
use App\Sales;
use App\DetailSales;
use App\Employee;
use Carbon\Carbon;

class TestingSummarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {        
    	$satuan = ['pack', 'karton'];

    	$employees = Employee::get();

    	$d = 30;

        // SALES HEADER       
    	foreach($employees as $i){

    		$type = 'Sell In';
    		if($i->position->level == 'spgmtc') $type = 'Sell Out';

    		$store_id = EmployeeStore::where('id_employee', ($i->id))->pluck('id_store')->toArray();

    		foreach ($store_id as $j) {
    			$sales = Sales::create([
        					'id_employee' => $i->id,
				            'id_store' => $j,
				            'date' => Carbon::parse('2018-11-'.$d),
				            'week' => Carbon::parse('2018-11-'.$d)->weekOfMonth,
				            'type' => $type,
				            'created_at' => Carbon::now(),
				            'updated_at' => Carbon::now(),
        				]);

    			$howMany = 20;
				if($i->position->level == 'spgmtc') $type = 50;

    			foreach(range(1, $howMany) as $k){
    				DetailSales::create([
						'id_sales' => $sales->id,
			            'id_product' => $k,
			            'qty' => rand(10, 200),
			            'qty_actual' => rand(1, 10),
			            'satuan' => $satuan[rand(0,1)],
			            'created_at' => Carbon::now(),
			            'updated_at' => Carbon::now(),
					]);
    			}
    		}

    	}

    }
}
