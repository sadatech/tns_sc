<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class EmployeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        DB::table('employees')->insert([
            'id_position' => rand(1,7),
            'id_agency' => rand(1,99),
            'id_timezone' => rand(1,3),
            'name' => 'Sada Employee',
            'nik' => '1010',
            'ktp' => '9'.rand(0000000,99999999),
            'email' => $faker->email,
            'status' => 'Stay',
            'joinAt' => Carbon::now(),
            'birthdate' => Carbon::now(),
            'gender' => 'Laki-laki',
            'education' => 'D3',
            'isResign' => 0,
            'rekening' => '9'.rand(0000000,99999999),
            'phone' => '0857'.rand(0000000,99999999),
            'password' => bcrypt('admin'),
            'foto_ktp' => 'default.png',
            'foto_profile' => 'default.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        foreach(range(0,98) as $i){
            $insert = DB::table('employees')->insert([
                'id_position' => rand(1,7),
                'id_agency' => rand(1,99),
                'id_timezone' => rand(1,3),
                'name' => $faker->name,
                'nik' => '200'.$i.rand(00000,99999),
                'ktp' => '9'.rand(0000000,99999999),
                'email' => $faker->email,
                'status' => 'Stay',
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
            DB::table('employee_stores')->insert([
                'id_store' => rand(1,99),
                'id_employee' => $i+1,
            ]);
        }
    }
}
}
