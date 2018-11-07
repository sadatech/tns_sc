<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductFokusesTable extends Migration
{
    public function up()
    {
        Schema::create('product_fokuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_product')->unsigned();
            // $table->integer('id_area')->unsigned()->nullable();
            // $table->enum('type',['TR','MR','ALL']);
            $table->string('from');
            $table->string('to')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('id_area')->references('id')->on('areas')->onUpdate('cascade')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('product_fokuses');
    }
}
