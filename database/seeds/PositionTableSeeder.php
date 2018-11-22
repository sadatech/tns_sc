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
                ['name' => 'SPG',           'level'=>'spgmtc'],
                ['name' => 'MD',            'level'=>'mdmtc'],
                ['name' => 'SPG Pasar',     'level'=>'spggtc'],
                ['name' => 'MD Pasar',      'level'=>'mdgtc'],
                ['name' => 'Demo Cooking',  'level'=>'dc'],
                ['name' => 'TL MTC',        'level'=>'tlmtc'],
                ['name' => 'TL GTC',        'level'=>'tlgtc'],
            ]);
        }
    	// $exists = Position::where('deleted_at',null)->count();
    	// if ($exists <= 0) {
	    //     DB::table('positions')->insert([
	    //         ['name' => 'SPG (Reguler)', 'level' => 'level 1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
	    //         ['name' => 'MD (Reguler)', 'level' => 'level 1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
	    //         ['name' => 'SPG (Pasar)', 'level' => 'level 1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
	    //         ['name' => 'MD (Pasar)', 'level' => 'level 1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
	    //         ['name' => 'Demo Coocking', 'level' => 'level 1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
	    //         ['name' => 'TL (Team Leader)', 'level' => 'level 2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
	    //     ]);
	    // }
    }
}
