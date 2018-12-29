<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductCompetitorOnDataPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detail_data_price', function (Blueprint $table) {
             $table->unsignedInteger('id_brand_copetitor')->after('price')->nullable();
             $table->string('isSasa')->after('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_data_price', function($table) {
            if (Schema::hasColumn('detail_data_price', 'id_brand_copetitor')) {
                $table->dropColumn('id_brand_copetitor');
            }
            if (Schema::hasColumn('detail_data_price', 'isSasa')) {
                $table->dropColumn('isSasa');
            }
        });
    }
}
