<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResignStoresTable extends Migration
{
    public function up()
    {
        Schema::create('resign_stores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_store')->unsigned();
            $table->integer('id_resign')->unsigned();
            $table->timestamps();

            $table->foreign('id_store')->references('id')->on('stores')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_resign')->references('id')->on('resigns')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('resign_stores');
    }
}
