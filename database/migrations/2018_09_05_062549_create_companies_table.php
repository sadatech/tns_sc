<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->text('logo');
            $table->string('name');
            $table->text('introduce')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('fax')->unique()->nullable();
            $table->text('address');
            $table->integer('id_province')->unsigned();
            $table->integer('id_city')->unsigned();
            $table->string('postal_code');
            $table->enum('typePrice', ['1', '2'])->default('2');
            //1 : Sellin - Sellout
            //2 : One Price
            $table->enum('typeStock', ['1', '2', '3'])->default('3');
            //1 : Sellin
            //2 : Sellout
            //3 : One Price
            $table->string('token')->unique();
            $table->timestamps();

            $table->foreign('id_province')->references('id')->on('provinces')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_city')->references('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
