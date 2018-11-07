<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePfsTable extends Migration
{
    public function up()
    {
        Schema::create('pfs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_category1')->unsigned();
            $table->integer('id_category2')->unsigned();
            $table->string('from');
            $table->string('to')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_category1')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_category2')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pfs');
    }
}
