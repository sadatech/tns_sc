<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceProfitInCashAdvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->integer('subsidi_sasa')->after('total_cost')->nullable();
            $table->integer('price_profit')->after('total_cost')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_advances', function($table) {
            $table->dropColumn(['price_profit']);
            $table->dropColumn(['subsidi_sasa']);
        });
    }
}
