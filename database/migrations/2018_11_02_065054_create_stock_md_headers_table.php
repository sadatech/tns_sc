<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockMdHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_md_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_employee')->unsigned();
            $table->integer('id_pasar')->unsigned();
            $table->string('stockist'); 
            $table->date('date');
            $table->integer('week'); 
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('stock_md_headers');
    }
}
