<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubAreasTable extends Migration
{
    public function up()
    {
        Schema::create('sub_areas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('id_area')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_area')->references('id')->on('areas')->onUpdate('cascade')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('sub_areas');
    }
}
