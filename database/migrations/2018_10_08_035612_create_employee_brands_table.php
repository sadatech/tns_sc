<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeBrandsTable extends Migration
{
    // public function up()
    // {
    //     Schema::create('employee_brands', function (Blueprint $table) {
    //         $table->increments('id');
    //         $table->integer('id_employee')->unsigned();
    //         $table->timestamps();

    //         $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
    //     });
    // }

    // public function down()
    // {
    //     Schema::dropIfExists('employee_brands');
    // }
}
