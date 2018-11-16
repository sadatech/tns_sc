<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class EmployeeStoreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$faker = Faker::create();
    	foreach(range(1,99) as $i){
    		// DB::table('employee_stores')->insert([
      //           'id_employee' => $i,
      //           'id_store' => rand(1,99),
      //           'created_at' => Carbon::now(),
      //           'updated_at' => Carbon::now()
      //       ]);
            DB::table('employee_store_gtcs')->insert([
                'id_employee'   => $i,
                'id_store_gtc'  => rand(1,99),
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            ]);
    	}

        // EMP STORE MD
        foreach(range(1,70) as $i){
            foreach(range(1,20) as $j){
                DB::table('employee_stores')->insert([
                    'id_employee' => $i,
                    'id_store' => $j,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }            
        }

        // EMP STORE SPG STAY
        foreach(range(1,30) as $i){
            DB::table('employee_stores')->insert([
                'id_employee' => $i+70,
                'id_store' => rand(1,99),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);          
        }

        // EMP STORE SPG MOBILE
        foreach(range(1,60) as $i){
            foreach(range(1,2) as $j){
                DB::table('employee_stores')->insert([
                    'id_employee' => $i+70+30,
                    'id_store' => $j,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }         
        }
    }
}
