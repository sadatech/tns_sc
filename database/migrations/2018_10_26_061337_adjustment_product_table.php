<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdjustmentProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function($table) {
            $table->unsignedInteger('stock_type_id')->after('name');
            $table->string('code')->after('name');

            $table->foreign('stock_type_id')->references('id')->on('product_stock_types');
        });

        Schema::create('sku_units', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('conversion_value');
            $table->timestamps();
        });

        Schema::create('product_units', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('sku_unit_id');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('sku_unit_id')->references('id')->on('sku_units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('product_units')) {
            Schema::table('product_units', function($table) {
                $table->dropForeign(['product_id']);
                $table->dropForeign(['sku_unit_id']);
            });
            Schema::dropIfExists('product_units');
        }

        Schema::dropIfExists('sku_units');

        Schema::table('products', function($table) {
            $table->dropForeign(['stock_type_id']);
            $table->dropColumn(['stock_type_id', 'code']);
        });
    }
}
