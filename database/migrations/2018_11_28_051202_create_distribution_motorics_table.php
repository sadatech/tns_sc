<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributionMotoricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distribution_motorics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_block')->unsigned();
            $table->integer('id_employee')->unsigned();
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_block')->references('id')->on('blocks')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
      
        });

        Schema::create('distribution_motoric_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_distribution')->unsigned();
            $table->integer('id_product')->unsigned();
            $table->integer('qty')->unsigned();
            $table->integer('qty_actual')->unsigned();
            $table->string('satuan');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_distribution')->references('id')->on('distribution_motorics')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::table('distribution_motoric_details', function (Blueprint $table) {
            $table->dropForeign(['id_distribution']);
            $table->dropForeign(['id_product']);            
        });
        Schema::dropIfExists('distribution_motoric_details');
        Schema::dropIfExists('distribution_motorics');
    }
}
