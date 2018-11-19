<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\MtcReportTemplate;
use App\Employee;
use App\EmployeeStore;

class ReportTemplateSeeder extends Seeder
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
				// if($i->position->level == 'spgmtc') $type = 50;

    			foreach(range(1, $howMany) as $k){
    				MtcReportTemplate::create([
						'id_employee' => $i->id,
			            'id_store' => $j,
			            'id_product' => $k,
			            'date' => '2018-11-30',
			            'created_at' => Carbon::now(),
			            'updated_at' => Carbon::now(),
					]);
    			}
    		}

    	}
    }
}
