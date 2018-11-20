<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableForSalesSpgPasar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_spg_pasars', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_pasar')->unsigned();
            $table->integer('id_employee')->unsigned();
            $table->date('date');
            $table->integer('week');
            $table->string('type');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_pasar')->references('id')->on('pasars')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sales_spg_pasar_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sales')->unsigned();
            $table->integer('id_product')->unsigned();
            $table->integer('qty');
            $table->integer('qty_actual');
            $table->string('satuan');
            $table->tinyInteger('is_pf');
            $table->tinyInteger('is_target');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_sales')->references('id')->on('sales_spg_pasars')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('sales_spg_pasar_details');
        Schema::dropIfExists('sales_spg_pasars');
    }
}
