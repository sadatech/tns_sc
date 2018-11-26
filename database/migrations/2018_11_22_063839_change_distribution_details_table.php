<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDistributionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('distribution_details', function (Blueprint $table) {
            $table->dropColumn(['value']);
            $table->integer('qty')->unsigned();
            $table->integer('qty_actual')->unsigned();
            $table->string('satuan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('distribution_details', function (Blueprint $table) {
            $table->dropColumn(['qty', 'qty_actual', 'satuan']);
            $table->tinyInteger('value');
        });
    }
}
