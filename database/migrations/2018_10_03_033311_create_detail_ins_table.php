<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailInsTable extends Migration
{
    public function up()
    {
        Schema::create('detail_ins', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sellin')->unsigned();
            $table->integer('id_product')->unsigned();
            $table->integer('price');
            $table->integer('qty');
            $table->tinyInteger('is_pf');
            $table->tinyInteger('is_target');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_sellin')->references('id')->on('sell_ins')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
   
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_ins');
    }
}
