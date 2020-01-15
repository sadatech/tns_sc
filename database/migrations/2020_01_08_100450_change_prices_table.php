<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('prices');
        Schema::create('product_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_product')->unsigned();
            $table->string('retailer_price', 20)->nullable();
            $table->string('consumer_price', 20)->nullable();
            $table->date('release');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('product_prices');
        Schema::create('prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_product')->unsigned();
            $table->string('price', 20)->nullable();
            $table->string('price_cs', 20)->nullable();
            $table->date('release');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}