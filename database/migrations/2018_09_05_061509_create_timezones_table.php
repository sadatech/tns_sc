<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimezonesTable extends Migration
{
    public function up()
    {
        Schema::create('timezones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('timezone');
            $table->timestamps();
        });

        DB::table('timezones')->insert([
            ['name' => 'WIB', 'timezone' => 'Asia/Jakarta'],
            ['name' => 'WITA', 'timezone' => 'Asia/Makassar'],
            ['name' => 'WIT', 'timezone' => 'Asia/Jayapura'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('timezones');
    }
}
