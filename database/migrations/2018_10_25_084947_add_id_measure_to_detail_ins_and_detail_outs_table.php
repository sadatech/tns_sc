<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdMeasureToDetailInsAndDetailOutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detail_ins', function (Blueprint $table) {
            $table->unsignedInteger('id_measure')->after('id_product')->nullable();
            $table->foreign('id_measure')->references('id')->on('measurement_units')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('detail_outs', function (Blueprint $table) {
            $table->unsignedInteger('id_measure')->after('id_product')->nullable();
            $table->foreign('id_measure')->references('id')->on('measurement_units')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_ins', function (Blueprint $table) {
            $table->dropForeign(['id_measure']);
            $table->dropColumn('id_measure');
        });
        Schema::table('detail_outs', function (Blueprint $table) {
            $table->dropForeign(['id_measure']);
            $table->dropColumn('id_measure');
        });
    }
}
