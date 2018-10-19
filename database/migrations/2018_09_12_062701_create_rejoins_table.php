<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRejoinsTable extends Migration
{
    public function up()
    {
        Schema::create('rejoins', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_employee')->unsigned();
            $table->dateTime('join_date');
            $table->string('alasan')->nullable();
            $table->timestamps();

            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }
 
    public function down()
    {
        Schema::dropIfExists('rejoins');
    }
}
