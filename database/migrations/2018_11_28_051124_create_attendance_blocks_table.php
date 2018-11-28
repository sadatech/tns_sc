<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_attendance')->unsigned();
            $table->integer('id_block')->unsigned()->nullable();
            $table->datetime('checkin');
            $table->datetime('checkout')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_block')->references('id')->on('blocks')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_attendance')->references('id')->on('attendances')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_blocks');
    }
}
