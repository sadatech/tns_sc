<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailOutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_outs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sellout')->unsigned();
            $table->integer('id_product')->unsigned();
            $table->integer('qty');
            $table->integer('price');
            $table->integer('is_pf');
            $table->timestamps();

            $table->foreign('id_sellout')->references('id')->on('sell_outs')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('detail_outs');
    }
}
