<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIsSatuanToOosAtStockMdDetaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_md_details', function($table) {
            $table->tinyInteger('oos')->after('id_product');
            $table->dropForeign(['id_satuan']);
            $table->dropColumn(['id_satuan']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_md_details', function($table) {
            $table->dropColumn(['oos']);
            $table->integer('id_satuan')->unsigned()->after('id_product');
            $table->foreign('id_satuan')->references('id')->on('sku_units')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
