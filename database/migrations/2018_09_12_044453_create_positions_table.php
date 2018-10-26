<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionsTable extends Migration
{
    public function up()
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->tinyInteger('level');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('positions')->insert([
            ['name' => 'SPG Reguler','level'=>1],
            ['name' => 'MD Reguler','level'=>2],
            ['name' => 'SPG Pasar','level'=>3],
            ['name' => 'MD Pasar','level'=>4],
            ['name' => 'Demo Cooking','level'=>5],
            ['name' => 'TL','level'=>6],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('positions');
    }
}
