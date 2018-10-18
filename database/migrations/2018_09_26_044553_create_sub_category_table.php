<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubCategoryTable extends Migration
{
    public function up()
    {
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('id_category')->unsigned();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_category')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_categories');
    }
}
