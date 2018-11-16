<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;
use App\Employee;
use App\EmployeeStore;
use App\Target;
use App\Product;
use App\Price;
use App\Sales;
use App\DetailSales;


class NewTestAchievementTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
      //   DB::table('employees')->insert([
      //       'id_position' => rand(1,7),
      //       'id_agency' => rand(1,99),
      //       'id_timezone' => rand(1,3),
      //       'name' => 'Sada Employee',
      //       'nik' => '1010',
      //       'ktp' => '9'.rand(0000000,99999999),
      //       'email' => $faker->email,
      //       'status' => 'Stay',
      //       'joinAt' => Carbon::now(),
      //       'gender' => 'Laki-laki',
      //       'education' => 'D3',
      //       'isResign' => 0,
      //       'rekening' => '9'.rand(0000000,99999999),
      //       'phone' => '0857'.rand(0000000,99999999),
      //       'password' => bcrypt('admin'),
      //       'created_at' => Carbon::now(),
      //       'updated_at' => Carbon::now()
      //   ]);
      //   foreach(range(0,98) as $i){
      //     DB::table('employees')->insert([
      //       'id_position' => rand(1,7),
      //       'id_agency' => rand(1,99),
      //       'id_timezone' => rand(1,3),
      //       'name' => $faker->name,
      //       'nik' => ''.rand(0000000,99999999),
      //       'ktp' => '9'.rand(0000000,99999999),
      //       'email' => $faker->email,
      //       'status' => 'Stay',
      //       'joinAt' => Carbon::now(),
      //       'gender' => 'Laki-laki',
      //       'education' => 'D3',
      //       'isResign' => 0,
      //       'rekening' => '9'.rand(0000000,99999999),
      //       'phone' => '0857'.rand(0000000,99999999),
      //       'password' => bcrypt('admin'),
      //       'created_at' => Carbon::now(),
      //       'updated_at' => Carbon::now()
      //   ]);
      // }

        // MD
        foreach(range(1,70) as $i){
          DB::table('employees')->insert([
            'id_position' => 2,
            'id_agency' => rand(1,99),
            'id_timezone' => rand(1,3),
            'name' => $faker->name,
            'nik' => ''.rand(0000000,99999999),
            'ktp' => '9'.rand(0000000,99999999),
            'email' => $faker->email,
            'status' => 'Mobile',
            'joinAt' => Carbon::now(),
            'birthdate' => Carbon::now(),
            'gender' => 'Laki-laki',
            'education' => 'D3',
            'isResign' => 0,
            'rekening' => '9'.rand(0000000,99999999),
            'phone' => '0857'.rand(0000000,99999999),
            'password' => bcrypt('admin'),
            'foto_ktp' => 'default.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

      }

      // SPG STAY
        foreach(range(1,30) as $i){
          DB::table('employees')->insert([
            'id_position' => 1,
            'id_agency' => rand(1,99),
            'id_timezone' => rand(1,3),
            'name' => $faker->name,
            'nik' => ''.rand(0000000,99999999),
            'ktp' => '9'.rand(0000000,99999999),
            'email' => $faker->email,
            'status' => 'Stay',
            'joinAt' => Carbon::now(),
            'gender' => 'Laki-laki',
            'education' => 'D3',
            'isResign' => 0,
            'rekening' => '9'.rand(0000000,99999999),
            'phone' => '0857'.rand(0000000,99999999),
            'password' => bcrypt('admin'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
      }

      // SPG MOBILE
        foreach(range(1,60) as $i){
          DB::table('employees')->insert([
            'id_position' => 1,
            'id_agency' => rand(1,99),
            'id_timezone' => rand(1,3),
            'name' => $faker->name,
            'nik' => ''.rand(0000000,99999999),
            'ktp' => '9'.rand(0000000,99999999),
            'email' => $faker->email,
            'status' => 'Mobile',
            'joinAt' => Carbon::now(),
            'gender' => 'Laki-laki',
            'education' => 'D3',
            'isResign' => 0,
            'rekening' => '9'.rand(0000000,99999999),
            'phone' => '0857'.rand(0000000,99999999),
            'password' => bcrypt('admin'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
      }




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



        
        for ($i=1; $i < 51; $i++) { 
            Product::create([
                'name' => 'Product ' . $i,
                'id_subcategory' => rand(1, 10),
                'id_brand' => 1,
                'code' => rand(10000, 100000),
                'stock_type_id' => rand(1, 2),
                'panel' => 'yes',
                'pcs'   => 1
            ]);
        }



        for ($i=1; $i < 51; $i++) { 
            Price::create([
                'id_product' => $i,
                'price' => rand(10000, 100000),
                'rilis' => Carbon::parse('2018-11-01')
            ]);
        }

        for ($i=1; $i < 51; $i++) { 
            Price::create([
                'id_product' => $i,
                'price' => rand(10000, 100000),
                'rilis' => Carbon::parse('2018-11-15')
            ]);
        }

        for ($i=1; $i < 51; $i++) { 
            Price::create([
                'id_product' => $i,
                'price' => rand(10000, 100000),
                'rilis' => Carbon::parse('2018-11-25')
            ]);
        }



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
}
