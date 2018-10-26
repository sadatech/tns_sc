<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_plandc')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_plandc')->references('id')->on('plan_dcs')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_employees');
    }
}
