<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_outlets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_attendance')->unsigned();
            $table->integer('id_outlet')->unsigned()->nullable();
            $table->datetime('checkin');
            $table->datetime('checkout')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_outlet')->references('id')->on('outlets')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('attendance_outlets');
    }
}
