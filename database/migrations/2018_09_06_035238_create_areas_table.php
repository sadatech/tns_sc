<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreasTable extends Migration
{
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('id_region')->unsigned();
            $table->timestamps();

            $table->foreign('id_region')->references('id')->on('regions')->onUpdate('cascade')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('areas');
    }
}
