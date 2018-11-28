<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesMotoricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_motorics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_block')->unsigned();
            $table->integer('id_employee')->unsigned();
            $table->date('date');
            $table->integer('week');
            $table->string('type');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_block')->references('id')->on('blocks')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sales_motoric_details', function (Blueprint $table) {
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

            $table->foreign('id_sales')->references('id')->on('sales_motorics')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::table('sales_motoric_details', function (Blueprint $table) {
            $table->dropForeign(['id_sales']);
            $table->dropForeign(['id_product']);            
        });
        Schema::dropIfExists('sales_motorics');
        Schema::dropIfExists('sales_motoric_details');
    }
}
