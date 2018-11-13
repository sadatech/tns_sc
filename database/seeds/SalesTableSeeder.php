<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\EmployeeStore;
use Faker\Factory as Faker;

class SalesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // SALES HEADER       
        foreach(range(1, 30) as $d){
        	$employee = 0;
	        foreach(range(1, 20) as $i){
	         $employee += 1;
	         $store_id = EmployeeStore::where('id_employee', ($employee))->first()->id_store;
	         DB::table('sales')->insert([
	            'id_employee' => $employee,
	            'id_store' => $store_id,
	            'date' => Carbon::parse('2018-11-'.$d),
	            'week' => Carbon::parse('2018-11-'.$d)->weekOfMonth,
	            'type' => 'Sell In',
	            'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now(),
	         ]);

	         DB::table('sales')->insert([
	            'id_employee' => $employee,
	            'id_store' => $store_id,
	            'date' => Carbon::parse('2018-11-'.$d),
	            'week' => Carbon::parse('2018-11-'.$d)->weekOfMonth,
	            'type' => 'Sell Out',
	            'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now(),
	         ]);

	        }
	    }

	    $satuan = ['pack', 'karton'];

	    // SALES DETAIL
	    foreach(range(1, 600) as $i){
	    	foreach(range(1, 10) as $j){
	         DB::table('detail_sales')->insert([
	            'id_sales' => $i,
	            'id_product' => $j,
	            'qty' => rand(1, 10),
	            'qty_actual' => rand(20, 100),
	            'satuan' => $satuan[rand(0,1)],
	            'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now(),
	         ]);

	         DB::table('detail_sales')->insert([
	            'id_sales' => $i,
	            'id_product' => $j,
	            'qty' => rand(1, 10),
	            'qty_actual' => rand(20, 100),
	            'satuan' => $satuan[rand(0,1)],
	            'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now(),
	         ]);

     		}
        }
    }
}
