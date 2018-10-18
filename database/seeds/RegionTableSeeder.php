<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class RegionTableSeeder extends Seeder
{
    public function run()
    {
        // DB::table('companies')->insert([
        //         "username" => "sadatech",
        //         "logo"  =>"default.png",
        //         "name"  =>"SADA TECHNOLOGY",
        //         "introduce" =>"introduce",
        //         "email" =>"sada@technologist.com",
        //         "phone" =>" 021-22086179 ",
        //         "fax"   =>" 021-22086179 ",
        //         "address"   =>"No. 9D, Jl. Radin Inten II, RT.8/RW.10, Duren Sawit",
        //         "id_province"   =>"31",
        //         "id_city"   =>"3172",
        //         "postal_code"   =>"13440",
        //         "token" =>"ngasalduludeh",
        //         "created_at"    => null,
        //         "updated_at"   => null
        //     ]);
        
        $faker = Faker::create();
        foreach(range(1,10) as $i){
            DB::table('regions')->insert([
                'name'          => $faker->state,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            ]);
        }
    }
}
