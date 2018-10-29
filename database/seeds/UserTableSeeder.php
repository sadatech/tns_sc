<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('user_role')->insert([
            ['level' => 'MasterAdmin'],
            ['level' => 'Administrator'],
            ['level' => 'User'],
        ]);
        DB::table('users')->insert([
            ['name' => 'Sadatech', 'email' => 'sada@gmail.com', 'password' => bcrypt('admin'), 'role_id' => '1', 'email_status' => 'verified'],
              ['name' => 'Sasa', 'email' => 'sasa@gmail.com', 'password' => bcrypt('admin'), 'role_id' => '2', 'email_status' => 'verified'],
              ['name' => 'SasaUser', 'email' => 'sasauser@gmail.com', 'password' => bcrypt('user123'), 'role_id' => '3', 'email_status' => 'verified'],
        ]);
    }
}
