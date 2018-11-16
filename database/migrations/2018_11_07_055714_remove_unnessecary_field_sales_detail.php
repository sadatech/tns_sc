<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUnnessecaryFieldSalesDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detail_sales', function (Blueprint $table) {
            //
            $table->dropColumn('price');
            $table->dropColumn('is_pf');
            $table->dropColumn('is_target');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_sales', function (Blueprint $table) {
            //
            $table->integer('price');
            $table->tinyInteger('is_pf');
            $table->tinyInteger('is_target');
        });
    }
}
