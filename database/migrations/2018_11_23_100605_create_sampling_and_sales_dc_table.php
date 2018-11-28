<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSamplingAndSalesDcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sampling_dcs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_employee')->unsigned();
            $table->date('date');
            $table->string('place');
            $table->integer('week');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sampling_dc_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sales')->unsigned();
            $table->integer('id_product')->unsigned();
            $table->integer('qty');
            $table->integer('qty_actual');
            $table->string('satuan');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_sales')->references('id')->on('sampling_dcs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sales_dcs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_employee')->unsigned();
            $table->date('date');
            $table->string('place');
            $table->integer('week');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sales_dc_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sales')->unsigned();
            $table->integer('id_product')->unsigned();
            $table->integer('qty');
            $table->integer('qty_actual');
            $table->string('satuan');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_sales')->references('id')->on('sales_dcs')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('sales_dc_details');
        Schema::dropIfExists('sales_dcs');
        Schema::dropIfExists('sampling_dc_details');
        Schema::dropIfExists('sampling_dcs');
    }
}
