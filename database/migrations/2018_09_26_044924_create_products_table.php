<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_subcategory')->unsigned();
            $table->integer('id_brand')->unsigned();
            $table->string('deskripsi')->nullable();
            $table->string('name');
            $table->string('panel')->nullable();
            $table->timestamps();

            $table->foreign('id_subcategory')->references('id')->on('sub_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_brand')->references('id')->on('brands')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
