<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TimeZoneTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('timezones')->insert([
            ['name' => 'WIB', 'timezone' => 'Asia/Jakarta'],
            ['name' => 'WITA', 'timezone' => 'Asia/Makassar'],
            ['name' => 'WIT', 'timezone' => 'Asia/Jayapura'],
        ]);
    }
}
