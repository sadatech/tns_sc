<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // public function up()
    // {
    //     Schema::create('import_queues', function (Blueprint $table) {
    //         $table->increments('id');
    //         $table->integer('id_employee')->unsigned();
    //         $table->date('date');
    //         $table->text('file')->nullable();
    //         $table->string('type')->nullable();
    //         $table->string('status')->nullable();
    //         $table->string('log')->nullable();
    //         $table->timestamps();

    //         $table->foreign('id_employee')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
    //     });
    // }

    // /**
    //  * Reverse the migrations.
    //  *
    //  * @return void
    //  */
    // public function down()
    // {
    //     Schema::dropIfExists('import_queues');
    // }
}
