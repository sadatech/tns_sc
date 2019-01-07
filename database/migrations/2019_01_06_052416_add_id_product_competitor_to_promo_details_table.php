<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdProductCompetitorToPromoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promo_details', function($table) {
            if (Schema::hasColumn('promo_details', 'id_product')) {
                $table->dropForeign(['id_product']);
                $table->dropColumn('id_product');
            }
        });

        Schema::table('promo_details', function (Blueprint $table) {

            $table->integer('id_product')->after('id_promo')->unsigned()->nullable();
            $table->integer('id_product_competitor')->after('id_promo')->unsigned()->nullable();

            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_product_competitor')->references('id')->on('product_competitors')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promo_details', function($table) {
            if (Schema::hasColumn('promo_details', 'id_product')) {
                $table->dropForeign(['id_product']);
                $table->dropColumn('id_product');
            }
            if (Schema::hasColumn('promo_details', 'id_product_competitor')) {
                $table->dropForeign(['id_product_competitor']);
                $table->dropColumn('id_product_competitor');
            }
        });

        Schema::table('promo_details', function (Blueprint $table) {

            $table->integer('id_product')->after('id_promo')->unsigned();

            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
