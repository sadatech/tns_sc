<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sales')->unsigned();
            $table->integer('id_product')->unsigned();
            $table->integer('price');
            $table->integer('qty');
            $table->integer('qty_actual');
            $table->string('satuan');
            $table->tinyInteger('is_pf');
            $table->tinyInteger('is_target');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_sales')->references('id')->on('sales')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('detail_sales');
    }
}
