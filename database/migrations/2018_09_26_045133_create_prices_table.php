<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_product')->unsigned();
            $table->string('price');
            $table->date('rilis');
            $table->enum('type_toko', ['TR', 'MR', 'ALL']);
            $table->enum('type_price', ['1', '2', '3']);
            //1 : sellin
            //2 : sellout
            //3 : oneprice
            $table->timestamps();

            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('prices');
    }
}