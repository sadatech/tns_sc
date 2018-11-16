<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockMdDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_md_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_stock')->unsigned();
            $table->integer('id_product')->unsigned();
            $table->tinyInteger('oos');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_stock')->references('id')->on('stock_md_headers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_md_details');
    }
}
