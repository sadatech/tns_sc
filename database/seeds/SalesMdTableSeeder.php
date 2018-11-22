<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;

class SalesMdTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$faker = Faker::create();
        // Sales MD
	    $satuan = ['pack', 'karton'];
        $emp = \App\Employee::where('id_position', 4)->first()->id;
        $pasar = array_column(\App\EmployeePasar::where('id_employee', $emp)->get(['id'])->toArray(),'id');
        $outlet = array_column(\App\Outlet::whereIn('id_employee_pasar', $pasar)->get(['id'])->toArray(),'id');
        $now = Carbon::now();
        foreach (range(1, Carbon::now()->endOfMonth()->day) as $d) {
        	foreach (range(1,38) as $s) {
	        	$sales = DB::table('sales_mds')->insertGetId([
	        		'id_outlet' => $outlet[rand(0,count($outlet)-1)],
	        		'id_employee' => $emp,
	        		'date' => Carbon::parse($now->year."-".$now->month."-".$d),
	        		'week' => Carbon::parse($now->year."-".$now->month."-".$d)->weekOfMonth,
	        		'type' => 'Sell In',
	        	]);
        		foreach (range(1,rand(2,4)) as $sd) {
        			DB::table('sales_md_details')->insert([
        				'id_sales' => $sales,
        				'id_product' => rand(1,10),
        				'qty' => rand(1,10),
        				'qty_actual' => rand(20,100),
        				'satuan' => $satuan[rand(0,1)],
        				'is_pf' => rand(0,1),
        				'is_target' => rand(0,1)
        			]);
        		}
        	}
        }
    }
}
