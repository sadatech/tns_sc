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
            $table->enum('level', ['level 1', 'level 2', 'level 3', 'level 4']);    
            $table->timestamps();

        });
        DB::table('users')->inser
    }

    public function down()
    {
        Schema::dropIfExists('positions');
    }
}
