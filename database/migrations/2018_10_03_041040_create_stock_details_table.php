<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('stock_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_stock')->unsigned();
            $table->integer('id_product')->unsigned();
            $table->integer('price');
            $table->integer('qty');
            $table->boolean('isPf');
            $table->timestamps();

            $table->foreign('id_stock')->references('id')->on('stocks')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_details');
    }
}
