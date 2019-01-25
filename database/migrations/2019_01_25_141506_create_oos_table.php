<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_store')->unsigned();
            $table->integer('id_employee')->unsigned();
            $table->date('date');
            $table->integer('week')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_store')->references('id')->on('stores')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('oos_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_oos')->unsigned();
            $table->integer('id_product')->unsigned();
            $table->integer('qty')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_oos')->references('id')->on('oos')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oos_details');
        Schema::dropIfExists('oos');
    }
}
