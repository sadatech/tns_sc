<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeSpvsTable extends Migration
{
    // public function up()
    // {
    //     Schema::create('employee_spvs', function (Blueprint $table) {
    //         $table->increments('id');
    //         $table->integer('id_employee')->unsigned();
    //         $table->integer('id_user')->unsigned();
    //         $table->timestamps();

    //         $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
    //         $table->foreign('id_user')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
    //     });
    // }
    
    // public function down()
    // {
    //     Schema::dropIfExists('employee_spvs');
    // }
}
