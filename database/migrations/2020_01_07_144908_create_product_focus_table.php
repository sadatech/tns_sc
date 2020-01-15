<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductFocusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_focus', function (Blueprint $table) {
            $table->increments('id');
            $table->date('from');
            $table->date('to');
            $table->integer('id_product')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade');
        });

        Schema::create('product_focus_areas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_product_focus')->unsigned();
            $table->integer('id_area')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_product_focus')->references('id')->on('product_focus')->onUpdate('cascade');
            $table->foreign('id_area')->references('id')->on('areas')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_focus_areas');
        Schema::dropIfExists('product_focus');
    }
}
