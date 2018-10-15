<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellInsTable extends Migration
{
    public function up()
    {
        Schema::create('sell_ins', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_store')->unsigned();
            $table->integer('id_employee')->unsigned();
            $table->date('date');
            $table->integer('week');    
            $table->timestamps();

            $table->foreign('id_store')->references('id')->on('stores')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
      
        });
    }

    public function down()
    {
        Schema::dropIfExists('sell_ins');
    }
}
