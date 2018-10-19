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
            $table->timestamps();

        });
        DB::table('users')->insert([
            ['name' => 'SPG'],
            ['name' => 'SPG Pasar'],
            ['name' => 'MD'],
            ['name' => 'MD Pasar']
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('positions');
    }
}
