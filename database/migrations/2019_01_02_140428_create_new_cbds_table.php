<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewCbdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_cbds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_outlet')->unsigned();
            $table->integer('id_employee')->unsigned();
            $table->date('date');
            $table->text('photo')->nullable();
            $table->tinyInteger('posm_shop_sign')->nullable();
            $table->tinyInteger('posm_others')->nullable();
            $table->tinyInteger('posm_hangering_mobile')->nullable();
            $table->tinyInteger('posm_poster')->nullable();
            $table->string('cbd_competitor_detail')->nullable();
            $table->tinyInteger('cbd_competitor')->nullable();
            $table->string('cbd_position')->nullable();
            $table->string('outlet_type')->nullable();
            $table->integer('total_hanger')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_outlet')->references('id')->on('outlets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_cbds');
    }
}
