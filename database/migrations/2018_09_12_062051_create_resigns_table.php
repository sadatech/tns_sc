<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResignsTable extends Migration
{
    public function up()
    {
        Schema::create('resigns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_employee')->unsigned();
            $table->date('resign_date');
            $table->date('effective');
            $table->text('alasan');
            $table->text('penjelasan');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('resigns');
    }
}
