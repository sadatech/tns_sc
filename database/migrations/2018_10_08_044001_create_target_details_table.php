<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTargetDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // public function up()
    // {
    //     Schema::create('target_details', function (Blueprint $table) {
    //         $table->increments('id');
    //         $table->integer('id_target')->unsigned();
    //         $table->integer('id_store')->unsigned();
    //         $table->integer('value');
    //         $table->integer('value_pf');
    //         $table->enum('type',['Sell In','Sell Out']);
    //         $table->timestamps();
    //         $table->softDeletes();

    //         $table->foreign('id_target')->references('id')->on('targets')->onUpdate('cascade')->onDelete('cascade');
    //         $table->foreign('id_store')->references('id')->on('stores')->onUpdate('cascade')->onDelete('cascade');
    //     });
    // }

    // /**
    //  * Reverse the migrations.
    //  *
    //  * @return void
    //  */
    // public function down()
    // {
    //     Schema::dropIfExists('target_details');
    // }
}
