<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteStockTypeIdOnProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['stock_type_id']);
            $table->dropForeign(['id_brand']);
            $table->dropColumn(['stock_type_id','id_brand']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_type_id')->after('name')->unsigned();
            $table->foreign('stock_type_id')->references('id')->on('product_stock_types')->onUpdate('cascade');
            $table->integer('id_brand')->after('id')->unsigned();
            $table->foreign('id_brand')->references('id')->on('brands')->onUpdate('cascade');
        });
    }
}
