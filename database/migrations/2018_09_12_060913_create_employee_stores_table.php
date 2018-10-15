<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeStoresTable extends Migration
{
    public function up()
    {
        Schema::create('employee_stores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_store')->unsigned();
            $table->integer('id_employee')->unsigned();
            $table->string('alokasi',5);
            $table->timestamps();

            $table->foreign('id_store')->references('id')->on('stores')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_stores');
    }
}
