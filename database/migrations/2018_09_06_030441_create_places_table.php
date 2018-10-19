<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesTable extends Migration
{
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_province')->unsigned();
            $table->integer('id_city')->unsigned();
            $table->string('code');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('address');
            $table->string('description');
            $table->timestamps();

            $table->foreign('id_province')->references('id')->on('provinces')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_city')->references('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('places');
    }
}
