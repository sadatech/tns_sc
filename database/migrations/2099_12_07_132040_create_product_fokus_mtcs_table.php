<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductFokusMtcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_fokus_mtcs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_product')->unsigned();
            $table->integer('id_channel')->unsigned()->nullable();
            $table->integer('id_area')->unsigned()->nullable();
            $table->date('from');
            $table->date('to');
            $table->timestamps();

            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_channel')->references('id')->on('channels')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_area')->references('id')->on('areas')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_fokus_mtcs');
    }
}
