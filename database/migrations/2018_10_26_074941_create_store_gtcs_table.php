<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreGtcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_gtcs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->enum('active',['yes','no'])->nullable();
            $table->integer('id_pasar')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_pasar')->references('id')->on('pasars')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_gtcs', function (Blueprint $table) {
            $table->dropForeign(['id_pasar']);
        });
        Schema::dropIfExists('store_gtcs');
    }
}
