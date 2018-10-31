<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductFokusMdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_fokus_mds', function (Blueprint $table) {
                        $table->increments('id');
            $table->integer('id_product')->unsigned();
            $table->integer('id_area')->unsigned()->nullable();
            $table->string('from');
            $table->string('to')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_area')->references('id')->on('areas')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_fokus_mds');
    }
}
