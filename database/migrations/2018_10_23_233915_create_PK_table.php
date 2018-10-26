<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePKTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_knowledges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('admin',150);
            $table->string('sender',150);
            $table->string('subject',150);
            $table->string('type',150);
            $table->text('filePDF');
            $table->unsignedInteger('target');
            $table->foreign('target')->references('id')->on('positions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_knowledges');
    }
}
