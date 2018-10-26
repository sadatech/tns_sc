<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailabilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('availability', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_store');
            $table->unsignedInteger('id_employee');
            $table->date('date');
            $table->timestamps();

            $table->foreign('id_store')->references('id')->on('stores');
            $table->foreign('id_employee')->references('id')->on('employees');
        });

        Schema::create('detail_availability', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_availability');
            $table->unsignedInteger('id_product');
            $table->unsignedSmallInteger('available')->default(0);
            $table->timestamps();

            $table->foreign('id_availability')->references('id')->on('availability');
            $table->foreign('id_product')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_availability', function (Blueprint $table) {
            $table->dropForeign(['id_availability']);
            $table->dropForeign(['id_product']);
        });
        Schema::dropIfExists('detail_availability');

        Schema::table('availability', function (Blueprint $table) {
            $table->dropForeign(['id_store']);
            $table->dropForeign(['id_employee']);
        });
        Schema::dropIfExists('availability');
    }
}
