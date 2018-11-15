<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFokusProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fokus_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_product')->unsigned();
            $table->integer('id_pf')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_pf')->references('id')->on('product_fokuses')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fokus_products');
    }
}
