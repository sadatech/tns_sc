<?php

use Illuminate\Database\Seeder;
use Auth;
use App\User;
use App\Company;

class CompaniesTableSeeder extends Seeder
{
    public function run()
    {
        $insert = Company::create([
            'email'       => str_random(20) . '@tirtayasa99.com',
            'id_province' => 91,
            'id_city'     => 9101,
            'logo'        => "default.png",
            'username'    => str_random(20) . 'Tirtayasa99',
            'name'        => str_random(20) . 'TirtayasaFoundation',
            'phone'       => str_random(20) . '082188884411',
            'fax'         => str_random(20) . '12345',
            'address'     => str_random(20) . 'Jalan Tirtayasa No.9, Jakarta Timur',
            'postal_code' => str_random(20) . '246801',
            'token'       => md5(base64_encode(str_random(16).date("Y-m-d h:i:sa")))
        ]);
    }
}
