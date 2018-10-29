<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeStoreGtcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_store_gtcs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_store_gtc')->unsigned();
            $table->integer('id_employee')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_store_gtc')->references('id')->on('store_gtcs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_store_gtcs', function (Blueprint $table) {
            $table->dropForeign(['id_employee']);
            $table->dropForeign(['id_store_gtc']);
        });
        Schema::dropIfExists('employee_store_gtcs');
    }
}
