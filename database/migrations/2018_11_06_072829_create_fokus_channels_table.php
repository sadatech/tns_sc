<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFokusChannelsTable extends Migration
{
    public function up()
    {
        Schema::create('fokus_channels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_channel')->unsigned();
            $table->integer('id_pf')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_channel')->references('id')->on('channels')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_pf')->references('id')->on('product_fokuses')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fokus_channels');
    }
}
