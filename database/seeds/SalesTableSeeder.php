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
	         DB::table('sell_ins')->insert([
	            'id_employee' => $employee,
	            'id_store' => $store_id,
	            'date' => Carbon::parse('2018-11-'.$d),
	            'week' => Carbon::parse('2018-11-'.$d)->weekOfMonth,
	            'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now(),
	         ]);

	         DB::table('sell_outs')->insert([
	            'id_employee' => $employee,
	            'id_store' => $store_id,
	            'date' => Carbon::parse('2018-11-'.$d),
	            'week' => Carbon::parse('2018-11-'.$d)->weekOfMonth,
	            'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now(),
	         ]);

	        }
	    }

	    // SALES DETAIL
	    foreach(range(1, 600) as $i){
	    	foreach(range(1, 10) as $j){
	         DB::table('detail_ins')->insert([
	            'id_sellin' => $i,
	            'id_product' => $j,
	            'qty' => rand(1, 10),
	            'id_measure' => 1,
	            'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now(),
	         ]);

	         DB::table('detail_outs')->insert([
	            'id_sellout' => $i,
	            'id_product' => $j,
	            'qty' => rand(1, 10),
	            'id_measure' => 1,
	            'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now(),
	         ]);

     		}
        }
    }
}
