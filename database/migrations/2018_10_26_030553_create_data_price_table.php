<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_price', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_store');
            $table->unsignedInteger('id_employee');
            $table->date('date');
            $table->timestamps();

            $table->foreign('id_store')->references('id')->on('stores');
            $table->foreign('id_employee')->references('id')->on('employees');
        });

        Schema::create('detail_data_price', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_data_price');
            $table->unsignedInteger('id_product');
            $table->double('price',8,2);
            $table->timestamps();

            $table->foreign('id_data_price')->references('id')->on('data_price');
            $table->foreign('id_product')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_price', function (Blueprint $table) {
            $table->dropForeign(['id_store']);
            $table->dropForeign(['id_employee']);
        });
        Schema::dropIfExists('data_price');

        Schema::table('detail_data_price', function (Blueprint $table) {
            $table->dropForeign(['id_data_price']);
            $table->dropForeign(['id_product']);
        });
        Schema::dropIfExists('detail_data_price');
    }
}
