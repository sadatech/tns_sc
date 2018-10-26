<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Position;

class PositionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$exists = Position::where('deleted_at',null)->count();
    	if ($exists <= 0) {
	        DB::table('positions')->insert([
	            ['name' => 'SPG', 'level' => 'spgmtc', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
	            ['name' => 'MD', 'level' => 'mdmtc', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
	            ['name' => 'SPG Pasar', 'level' => 'spggtc', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
	            ['name' => 'MD Pasar', 'level' => 'mdgtc', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
	            ['name' => 'Demo Cooking', 'level' => 'dc', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
	            ['name' => 'Team Leader MTC', 'level' => 'tlmtc', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Team Leader GTC', 'level' => 'tlgtc', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]

	        ]);
	    }
    }
}
