<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTargetDcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_dcs', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('id_employee')->unsigned();
            $table->integer('id_subarea')->unsigned();
            $table->date('rilis');
            $table->integer('value');
            $table->integer('value_pf');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_subarea')->references('id')->on('sub_areas')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('target_dcs');
    }
}
