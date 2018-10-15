<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->text('photo')->nullable();
            $table->string('name1');
            $table->string('name2')->nullable();
            $table->string('address');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->integer('id_account')->unsigned();
            $table->integer('id_subarea')->unsigned();
            $table->string('is_vito');
            $table->string('store_panel');
            $table->string('sales_tier');
            $table->string('coverage');
            $table->string('delivery');
            $table->timestamps();

            $table->foreign('id_account')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_subarea')->references('id')->on('sub_areas')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
