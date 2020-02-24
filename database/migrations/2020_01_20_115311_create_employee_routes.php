<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_route')->unsigned();
            $table->integer('id_employee')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_route')->references('id')->on('routes')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('employee_routes');
    }
}
