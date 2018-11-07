<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFokusAreasTable extends Migration
{
    public function up()
    {
        Schema::create('fokus_areas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_area')->unsigned()->nullable();
            $table->integer('id_pf')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_area')->references('id')->on('areas')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_pf')->references('id')->on('product_fokuses')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fokus_areas');
    }
}
