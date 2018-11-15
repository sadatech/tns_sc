<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_outlet')->unsigned();
            $table->integer('id_employee')->unsigned();
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_outlet')->references('id')->on('outlets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
      
        });

        Schema::create('distribution_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_distribution')->unsigned();
            $table->integer('id_product')->unsigned();
            $table->tinyInteger('value');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_distribution')->references('id')->on('distributions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_product')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('distribution_details', function (Blueprint $table) {
            $table->dropForeign(['id_distribution']);
            $table->dropForeign(['id_product']);            
        });
        Schema::dropIfExists('distribution_details');
        Schema::dropIfExists('distributions');
    }
}
