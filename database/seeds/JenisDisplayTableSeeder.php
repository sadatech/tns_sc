<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class JenisDisplayTableSeeder extends Seeder
{
  public function run()
  {
    DB::table('jenis_displays')->insert([
      ['name' => 'Chiller'],
      ['name' => 'Rak'],
      ['name' => 'Power Wing'],
      ['name' => 'Rak Gondola'],
      ['name' => 'Endcap'],
      ['name' => 'Floor'],
      ['name' => 'Clipstripe'],
    ]);
  }
}
